<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiTrait;
use App\Http\Traits\RequiresDoctorUser;
use App\Http\Traits\SerializesDentalApiData;
use App\Models\Activity;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Report;
use App\Models\Reservation;
use App\Models\Scan;
use App\Support\ReservationStatusCatalog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class DoctorAccountController extends Controller
{
    use ApiTrait;
    use RequiresDoctorUser;
    use SerializesDentalApiData;

    public function show()
    {
        $doctor = $this->requireDoctor();
        if (!$doctor instanceof Doctor) {
            return $doctor;
        }

        return $this->successResponse(
            $this->serializeDoctor($doctor->load('specialties')),
            'Doctor profile retrieved successfully'
        );
    }

    public function update(Request $request)
    {
        $doctor = $this->requireDoctor();
        if (!$doctor instanceof Doctor) {
            return $doctor;
        }

        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => ['sometimes', 'email', Rule::unique('doctors', 'email')->ignore($doctor->id)],
            'phone' => 'sometimes|nullable|string|max:30',
            'specialty_ids' => 'sometimes|array',
            'specialty_ids.*' => 'integer|exists:specialties,id',
        ]);

        $updateData = [];

        foreach (['name', 'email', 'phone'] as $field) {
            if (array_key_exists($field, $data)) {
                $updateData[$field] = $data[$field];
            }
        }

        if ($updateData !== []) {
            $doctor->update($updateData);
        }

        if (array_key_exists('specialty_ids', $data)) {
            $doctor->specialties()->sync($data['specialty_ids'] ?? []);
        }

        Activity::create([
            'description' => "Doctor {$doctor->name} updated the profile",
            'type' => 'doctor_updated',
        ]);

        return $this->successResponse(
            $this->serializeDoctor($doctor->fresh()->load('specialties')),
            'Doctor profile updated successfully'
        );
    }

    public function updatePassword(Request $request)
    {
        $doctor = $this->requireDoctor();
        if (!$doctor instanceof Doctor) {
            return $doctor;
        }

        $request->merge([
            'current_password' => $request->input('current_password', $request->input('old_password')),
            'new_password' => $request->input('new_password', $request->input('password')),
            'new_password_confirmation' => $request->input('new_password_confirmation', $request->input('password_confirmation')),
        ]);

        $data = $request->validate([
            'current_password' => 'required|string|min:6',
            'new_password' => 'required|string|min:6|confirmed|different:current_password',
        ]);

        if (!Hash::check($data['current_password'], $doctor->password)) {
            return $this->errorResponse([], 'Current password is incorrect', 422);
        }

        if (Hash::check($data['new_password'], $doctor->password)) {
            return $this->errorResponse([], 'New password must be different from current password', 422);
        }

        $doctor->update([
            'password' => $data['new_password'],
        ]);

        Activity::create([
            'description' => "Doctor {$doctor->name} updated the password",
            'type' => 'doctor_updated',
        ]);

        return $this->successResponse([], 'Doctor password updated successfully');
    }

    public function patients()
    {
        $doctor = $this->requireDoctor();
        if (!$doctor instanceof Doctor) {
            return $doctor;
        }

        $patients = Patient::with('doctor.specialties')
            ->withCount(['reports', 'reservations'])
            ->where('doctor_id', $doctor->id)
            ->orderBy('name')
            ->get()
            ->map(fn (Patient $patient) => $this->serializePatient($patient))
            ->values();

        return $this->successResponse($patients, 'Doctor patients retrieved successfully');
    }

    public function statistics()
    {
        $doctor = $this->requireDoctor();
        if (!$doctor instanceof Doctor) {
            return $doctor;
        }

        $reservations = Reservation::where('doctor_id', $doctor->id)->get();

        return $this->successResponse([
            'profile' => $this->serializeDoctor($doctor->load('specialties')),
            'stats' => [
                'assigned_patients' => Patient::where('doctor_id', $doctor->id)->count(),
                'pending_reviews' => Scan::where('doctor_id', $doctor->id)->where('status', 'pending')->count(),
                'total_reports' => Report::where('doctor_id', $doctor->id)->count(),
                'total_reservations' => $reservations->count(),
                'pending_reservations' => $reservations->where('status', ReservationStatusCatalog::PENDING)->count(),
                'accepted_reservations' => $reservations->where('status', ReservationStatusCatalog::ACCEPTED)->count(),
                'refused_reservations' => $reservations->where('status', ReservationStatusCatalog::REFUSED)->count(),
                'cancelled_reservations' => $reservations->where('status', ReservationStatusCatalog::CANCELLED)->count(),
            ],
        ], 'Doctor statistics retrieved successfully');
    }

    public function logout(Request $request)
    {
        $doctor = $this->requireDoctor();
        if (!$doctor instanceof Doctor) {
            return $doctor;
        }

        if (!$request->user()?->currentAccessToken()) {
            return $this->errorResponse([], 'No active doctor token found', 401);
        }

        $request->user()->currentAccessToken()->delete();

        return $this->successResponse([], 'Doctor logout successful');
    }

    public function destroy()
    {
        $doctor = $this->requireDoctor();
        if (!$doctor instanceof Doctor) {
            return $doctor;
        }

        $name = $doctor->name;
        $doctor->tokens()->delete();
        $doctor->delete();

        Activity::create([
            'description' => "Doctor {$name} deleted the account",
            'type' => 'doctor_deleted',
        ]);

        return $this->successResponse([], 'Doctor account deleted successfully');
    }
}
