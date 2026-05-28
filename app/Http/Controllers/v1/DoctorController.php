<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiTrait;
use App\Models\Doctor;
use App\Models\Activity;
use Illuminate\Http\Request;

class DoctorController extends Controller
{
    use ApiTrait;

    /**
     * Get all doctors.
     */
    public function index()
    {
        $doctors = Doctor::orderBy('name')->get();
        return $this->successResponse($doctors, 'Doctors retrieved successfully');
    }

    /**
     * Store a new doctor.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $doctor = Doctor::create([
            'name' => $request->name
        ]);

        // Log activity
        Activity::create([
            'description' => "Doctor {$doctor->name} was added",
            'type' => 'doctor_added'
        ]);

        return $this->successResponse($doctor, 'Doctor added successfully');
    }

    /**
     * Delete a doctor.
     */
    public function destroy($id)
    {
        $doctor = Doctor::find($id);
        
        if (!$doctor) {
            return $this->errorResponse([], 'Doctor not found', 404);
        }

        $name = $doctor->name;
        $doctor->delete();

        // Log activity
        Activity::create([
            'description' => "Doctor {$name} was deleted",
            'type' => 'doctor_deleted'
        ]);

        return $this->successResponse([], 'Doctor deleted successfully');
    }
}
