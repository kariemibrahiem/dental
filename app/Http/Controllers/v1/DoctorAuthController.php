<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiTrait;
use App\Models\Doctor;
use App\Models\Specialty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class DoctorAuthController extends Controller
{
    use ApiTrait;

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
            'doctor' => $doctor->load('specialties'),
            'token' => $token,
        ], 'Doctor logged in successfully');
    }

    /**
     * Get authenticated Doctor profile.
     */
    public function profile()
    {
        $doctor = auth()->user();
        
        if (!$doctor || !($doctor instanceof Doctor)) {
            return $this->errorResponse([], 'Unauthorized or not a doctor', 401);
        }

        return $this->successResponse($doctor->load('specialties'), 'Doctor profile retrieved successfully');
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
