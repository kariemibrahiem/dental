<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiTrait;
use App\Http\Traits\RequiresPatientUser;
use App\Http\Traits\SerializesDentalApiData;
use App\Models\Activity;
use App\Models\Patient;
use App\Models\Report;
use App\Models\Reservation;
use App\Models\Scan;
use App\Support\DentalCaseCatalog;
use App\Support\ReservationStatusCatalog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class PatientAccountController extends Controller
{
    use ApiTrait;
    use RequiresPatientUser;
    use SerializesDentalApiData;

    public function show()
    {
        $patient = $this->requirePatient();
        if (!$patient instanceof Patient) {
            return $patient;
        }

        return $this->successResponse(
            $this->serializePatient($patient->load('doctor.specialties')),
            'Patient profile retrieved successfully'
        );
    }

    public function update(Request $request)
    {
        $patient = $this->requirePatient();
        if (!$patient instanceof Patient) {
            return $patient;
        }

        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => ['sometimes', 'email', Rule::unique('patients', 'email')->ignore($patient->id)],
            'phone' => 'sometimes|nullable|string|max:30',
            'doctor_id' => 'sometimes|nullable|exists:doctors,id',
        ]);

        if ($data !== []) {
            $patient->update($data);
        }

        Activity::create([
            'description' => "Patient {$patient->name} updated the profile",
            'type' => 'patient_updated',
        ]);

        return $this->successResponse(
            $this->serializePatient($patient->fresh()->load('doctor.specialties')),
            'Patient profile updated successfully'
        );
    }

    public function updatePassword(Request $request)
    {
        $patient = $this->requirePatient();
        if (!$patient instanceof Patient) {
            return $patient;
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

        if (!Hash::check($data['current_password'], $patient->password)) {
            return $this->errorResponse([], 'Current password is incorrect', 422);
        }

        if (Hash::check($data['new_password'], $patient->password)) {
            return $this->errorResponse([], 'New password must be different from current password', 422);
        }

        $patient->update([
            'password' => $data['new_password'],
        ]);

        Activity::create([
            'description' => "Patient {$patient->name} updated the password",
            'type' => 'patient_updated',
        ]);

        return $this->successResponse([], 'Patient password updated successfully');
    }

    public function statistics()
    {
        $patient = $this->requirePatient();
        if (!$patient instanceof Patient) {
            return $patient;
        }

        $scans = Scan::where('patient_id', $patient->id)->get();
        $reservations = Reservation::where('patient_id', $patient->id)->get();
        $reportsCount = Report::where('patient_id', $patient->id)->count();

        $healthyScans = $scans->filter(
            fn ($scan) => DentalCaseCatalog::normalize($scan->ai_result) === DentalCaseCatalog::HEALTHY
        )->count();
        $cavityScans = $scans->filter(
            fn ($scan) => DentalCaseCatalog::normalize($scan->ai_result) === DentalCaseCatalog::CAVITY
        )->count();
        $infectionScans = $scans->filter(
            fn ($scan) => DentalCaseCatalog::normalize($scan->ai_result) === DentalCaseCatalog::INFECTION
        )->count();

        return $this->successResponse([
            'profile' => $this->serializePatient($patient->load('doctor.specialties')),
            'stats' => [
                'total_scans' => $scans->count(),
                'healthy_scans' => $healthyScans,
                'cavity_scans' => $cavityScans,
                'infection_scans' => $infectionScans,
                'risk_scans' => $cavityScans + $infectionScans,
                'total_reports' => $reportsCount,
                'total_reservations' => $reservations->count(),
                'pending_reservations' => $reservations->where('status', ReservationStatusCatalog::PENDING)->count(),
                'accepted_reservations' => $reservations->where('status', ReservationStatusCatalog::ACCEPTED)->count(),
                'refused_reservations' => $reservations->where('status', ReservationStatusCatalog::REFUSED)->count(),
                'cancelled_reservations' => $reservations->where('status', ReservationStatusCatalog::CANCELLED)->count(),
            ],
        ], 'Patient statistics retrieved successfully');
    }

    public function logout(Request $request)
    {
        $patient = $this->requirePatient();
        if (!$patient instanceof Patient) {
            return $patient;
        }

        if (!$request->user()?->currentAccessToken()) {
            return $this->errorResponse([], 'No active patient token found', 401);
        }

        $request->user()->currentAccessToken()->delete();

        return $this->successResponse([], 'Patient logout successful');
    }

    public function destroy()
    {
        $patient = $this->requirePatient();
        if (!$patient instanceof Patient) {
            return $patient;
        }

        $name = $patient->name;
        $patient->tokens()->delete();
        $patient->delete();

        Activity::create([
            'description' => "Patient {$name} deleted the account",
            'type' => 'patient_deleted',
        ]);

        return $this->successResponse([], 'Patient account deleted successfully');
    }
}
