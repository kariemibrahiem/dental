<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiTrait;
use App\Http\Traits\RequiresDoctorUser;
use App\Http\Traits\SerializesDentalApiData;
use App\Models\Activity;
use App\Models\Doctor;
use App\Models\Reservation;
use App\Support\ReservationStatusCatalog;
use Illuminate\Http\Request;

class DoctorReservationController extends Controller
{
    use ApiTrait;
    use RequiresDoctorUser;
    use SerializesDentalApiData;

    public function index(Request $request)
    {
        $doctor = $this->requireDoctor();
        if (!$doctor instanceof Doctor) {
            return $doctor;
        }

        $query = Reservation::with('patient.doctor')
            ->where('doctor_id', $doctor->id)
            ->latest();

        $status = $request->query('status');
        if ($status && in_array($status, ReservationStatusCatalog::all(), true)) {
            $query->where('status', $status);
        }

        $reservations = $query->get()
            ->map(fn (Reservation $reservation) => $this->serializeReservation($reservation))
            ->values();

        return $this->successResponse($reservations, 'Doctor reservations retrieved successfully');
    }

    public function show(string $id)
    {
        $doctor = $this->requireDoctor();
        if (!$doctor instanceof Doctor) {
            return $doctor;
        }

        $reservation = Reservation::with('patient.doctor', 'doctor')
            ->where('doctor_id', $doctor->id)
            ->find($id);

        if (!$reservation) {
            return $this->errorResponse([], 'Reservation not found', 404);
        }

        return $this->successResponse(
            $this->serializeReservation($reservation),
            'Reservation retrieved successfully'
        );
    }

    public function accept(Request $request, string $id)
    {
        $doctor = $this->requireDoctor();
        if (!$doctor instanceof Doctor) {
            return $doctor;
        }

        $reservation = Reservation::where('doctor_id', $doctor->id)->find($id);

        if (!$reservation) {
            return $this->errorResponse([], 'Reservation not found', 404);
        }

        if ($reservation->status === ReservationStatusCatalog::CANCELLED) {
            return $this->errorResponse([], 'Cancelled reservations cannot be accepted', 422);
        }

        $data = $request->validate([
            'status_notes' => 'sometimes|nullable|string',
        ]);

        $reservation->update([
            'status' => ReservationStatusCatalog::ACCEPTED,
            'status_notes' => $data['status_notes'] ?? 'Accepted by doctor',
        ]);

        Activity::create([
            'description' => "Doctor {$doctor->name} accepted reservation #{$reservation->id}",
            'type' => 'doctor_updated',
        ]);

        return $this->successResponse(
            $this->serializeReservation($reservation->fresh()->load('patient', 'doctor')),
            'Reservation accepted successfully'
        );
    }

    public function refuse(Request $request, string $id)
    {
        $doctor = $this->requireDoctor();
        if (!$doctor instanceof Doctor) {
            return $doctor;
        }

        $reservation = Reservation::where('doctor_id', $doctor->id)->find($id);

        if (!$reservation) {
            return $this->errorResponse([], 'Reservation not found', 404);
        }

        if ($reservation->status === ReservationStatusCatalog::CANCELLED) {
            return $this->errorResponse([], 'Cancelled reservations cannot be refused', 422);
        }

        $data = $request->validate([
            'reason' => 'sometimes|nullable|string',
        ]);

        $reservation->update([
            'status' => ReservationStatusCatalog::REFUSED,
            'status_notes' => $data['reason'] ?? 'Refused by doctor',
        ]);

        Activity::create([
            'description' => "Doctor {$doctor->name} refused reservation #{$reservation->id}",
            'type' => 'doctor_updated',
        ]);

        return $this->successResponse(
            $this->serializeReservation($reservation->fresh()->load('patient', 'doctor')),
            'Reservation refused successfully'
        );
    }

    public function updateTime(Request $request, string $id)
    {
        $doctor = $this->requireDoctor();
        if (!$doctor instanceof Doctor) {
            return $doctor;
        }

        $reservation = Reservation::where('doctor_id', $doctor->id)->find($id);

        if (!$reservation) {
            return $this->errorResponse([], 'Reservation not found', 404);
        }

        if (in_array($reservation->status, ReservationStatusCatalog::refusedOrCancelled(), true)) {
            return $this->errorResponse([], 'This reservation can no longer be rescheduled', 422);
        }

        $data = $request->validate([
            'reservation_time' => 'required|date',
            'status_notes' => 'sometimes|nullable|string',
        ]);

        $reservation->update([
            'reservation_time' => $data['reservation_time'],
            'status_notes' => $data['status_notes'] ?? $reservation->status_notes,
        ]);

        Activity::create([
            'description' => "Doctor {$doctor->name} updated reservation time for #{$reservation->id}",
            'type' => 'doctor_updated',
        ]);

        return $this->successResponse(
            $this->serializeReservation($reservation->fresh()->load('patient', 'doctor')),
            'Reservation time updated successfully'
        );
    }
}
