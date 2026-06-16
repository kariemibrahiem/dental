<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiTrait;
use App\Http\Traits\RequiresPatientUser;
use App\Http\Traits\SerializesDentalApiData;
use App\Models\Activity;
use App\Models\Patient;
use App\Models\Reservation;
use App\Support\ReservationStatusCatalog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PatientReservationController extends Controller
{
    use ApiTrait;
    use RequiresPatientUser;
    use SerializesDentalApiData;

    public function index(Request $request)
    {
        $patient = $this->requirePatient();
        if (!$patient instanceof Patient) {
            return $patient;
        }

        $query = Reservation::with('doctor.specialties')
            ->where('patient_id', $patient->id)
            ->latest();

        $status = $request->query('status');
        if ($status && in_array($status, ReservationStatusCatalog::all(), true)) {
            $query->where('status', $status);
        }

        $reservations = $query->get()
            ->map(fn (Reservation $reservation) => $this->serializeReservation($reservation))
            ->values();

        return $this->successResponse($reservations, 'Patient reservations retrieved successfully');
    }

    public function store(Request $request)
    {
        $patient = $this->requirePatient();
        if (!$patient instanceof Patient) {
            return $patient;
        }

        $data = $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'reservation_time' => 'nullable|date',
            'image' => 'nullable|image|max:4096',
        ]);

        $imagePath = $request->file('image')?->store('reservations', 'public');

        $reservation = Reservation::create([
            'patient_id' => $patient->id,
            'doctor_id' => $data['doctor_id'],
            'title' => $data['title'],
            'description' => $data['description'],
            'image_path' => $imagePath,
            'reservation_time' => $data['reservation_time'] ?? null,
            'status' => ReservationStatusCatalog::PENDING,
        ]);

        if (!$patient->doctor_id) {
            $patient->update(['doctor_id' => $data['doctor_id']]);
        }

        Activity::create([
            'description' => "Patient {$patient->name} created reservation #{$reservation->id}",
            'type' => 'patient_added',
        ]);

        return $this->successResponse(
            $this->serializeReservation($reservation->load('doctor', 'patient')),
            'Reservation created successfully'
        );
    }

    public function show(string $id)
    {
        $patient = $this->requirePatient();
        if (!$patient instanceof Patient) {
            return $patient;
        }

        $reservation = Reservation::with('doctor.specialties', 'patient')
            ->where('patient_id', $patient->id)
            ->find($id);

        if (!$reservation) {
            return $this->errorResponse([], 'Reservation not found', 404);
        }

        return $this->successResponse(
            $this->serializeReservation($reservation),
            'Reservation retrieved successfully'
        );
    }

    public function update(Request $request, string $id)
    {
        $patient = $this->requirePatient();
        if (!$patient instanceof Patient) {
            return $patient;
        }

        $reservation = Reservation::where('patient_id', $patient->id)->find($id);

        if (!$reservation) {
            return $this->errorResponse([], 'Reservation not found', 404);
        }

        if (in_array($reservation->status, ReservationStatusCatalog::refusedOrCancelled(), true)) {
            return $this->errorResponse([], 'This reservation can no longer be updated', 422);
        }

        $data = $request->validate([
            'doctor_id' => 'sometimes|exists:doctors,id',
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'reservation_time' => 'sometimes|nullable|date',
            'image' => 'sometimes|nullable|image|max:4096',
        ]);

        $updateData = [];

        foreach (['doctor_id', 'title', 'description', 'reservation_time'] as $field) {
            if (array_key_exists($field, $data)) {
                $updateData[$field] = $data[$field];
            }
        }

        if ($request->hasFile('image')) {
            if ($reservation->image_path) {
                Storage::disk('public')->delete($reservation->image_path);
            }

            $updateData['image_path'] = $request->file('image')->store('reservations', 'public');
        }

        if ($updateData !== []) {
            $updateData['status'] = ReservationStatusCatalog::PENDING;
            $updateData['status_notes'] = null;
            $reservation->update($updateData);
        }

        Activity::create([
            'description' => "Patient {$patient->name} updated reservation #{$reservation->id}",
            'type' => 'patient_updated',
        ]);

        return $this->successResponse(
            $this->serializeReservation($reservation->fresh()->load('doctor', 'patient')),
            'Reservation updated successfully'
        );
    }

    public function destroy(string $id)
    {
        $patient = $this->requirePatient();
        if (!$patient instanceof Patient) {
            return $patient;
        }

        $reservation = Reservation::where('patient_id', $patient->id)->find($id);

        if (!$reservation) {
            return $this->errorResponse([], 'Reservation not found', 404);
        }

        if ($reservation->status === ReservationStatusCatalog::CANCELLED) {
            return $this->successResponse(
                $this->serializeReservation($reservation->load('doctor', 'patient')),
                'Reservation already cancelled'
            );
        }

        $reservation->update([
            'status' => ReservationStatusCatalog::CANCELLED,
            'status_notes' => 'Cancelled by patient',
        ]);

        Activity::create([
            'description' => "Patient {$patient->name} cancelled reservation #{$reservation->id}",
            'type' => 'patient_updated',
        ]);

        return $this->successResponse(
            $this->serializeReservation($reservation->fresh()->load('doctor', 'patient')),
            'Reservation cancelled successfully'
        );
    }
}
