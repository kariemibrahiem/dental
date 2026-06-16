<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiTrait;
use App\Http\Traits\RequiresPatientUser;
use App\Http\Traits\SerializesDentalApiData;
use App\Models\Patient;
use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PatientAuthController extends Controller
{
    use ApiTrait;
    use RequiresPatientUser;
    use SerializesDentalApiData;

    /**
     * Register a new patient.
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:patients,email',
            'phone' => 'nullable|string',
            'password' => 'required|string|min:6',
            'doctor_id' => 'nullable|exists:doctors,id',
        ]);

        $patient = Patient::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => $request->password, // automatically hashed by model set attribute
            'doctor_id' => $request->doctor_id,
        ]);

        // Log activity
        Activity::create([
            'description' => "New Patient {$patient->name} registered",
            'type' => 'patient_added'
        ]);

        $token = $patient->createToken('Patient API Token')->plainTextToken;

        return $this->successResponse([
            'patient' => $this->serializePatient($patient->load('doctor.specialties')),
            'token' => $token,
        ], 'Patient registered and logged in successfully');
    }

    /**
     * Authenticate Patient & return Sanctum token.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        $patient = Patient::where('email', $request->email)->first();

        if (!$patient || !Hash::check($request->password, $patient->password)) {
            return $this->errorResponse([], 'Invalid credentials', 401);
        }

        $token = $patient->createToken('Patient API Token')->plainTextToken;

        return $this->successResponse([
            'patient' => $this->serializePatient($patient->load('doctor.specialties')),
            'token' => $token,
        ], 'Patient logged in successfully');
    }

    /**
     * Get authenticated Patient profile.
     */
    public function profile()
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
}
