<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiTrait;
use App\Http\Traits\RequiresDoctorUser;
use App\Http\Traits\SerializesDentalApiData;
use App\Models\Doctor;
use App\Models\Specialty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class DoctorAuthController extends Controller
{
    use ApiTrait;
    use RequiresDoctorUser;
    use SerializesDentalApiData;

    /**
     * Authenticate Doctor & return Sanctum token.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        $doctor = Doctor::where('email', $request->email)->first();

        if (!$doctor || !Hash::check($request->password, $doctor->password)) {
            return $this->errorResponse([], 'Invalid credentials', 401);
        }

        $token = $doctor->createToken('Doctor API Token')->plainTextToken;

        return $this->successResponse([
            'doctor' => $this->serializeDoctor($doctor->load('specialties')),
            'token' => $token,
        ], 'Doctor logged in successfully');
    }

    /**
     * Get authenticated Doctor profile.
     */
    public function profile()
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

    /**
     * Public doctor directory for patient-facing flows.
     */
    public function directory()
    {
        $doctors = Doctor::with('specialties')
            ->withCount(['patients', 'reports', 'reservations'])
            ->orderBy('name')
            ->get()
            ->map(fn (Doctor $doctor) => $this->serializeDoctor($doctor))
            ->values();

        return $this->successResponse($doctors, 'Doctors retrieved successfully');
    }

    /**
     * Get list of all dental specialties.
     */
    public function specialties()
    {
        $specialties = Specialty::orderBy('name')->get();
        return $this->successResponse($specialties, 'Specialties retrieved successfully');
    }
}
