<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiTrait;
use App\Models\Patient;
use App\Models\Activity;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PatientController extends Controller
{
    use ApiTrait;

    /**
     * Get all patients with their doctors.
     */
    public function index()
    {
        $patients = Patient::with('doctor')->latest()->get();
        return $this->successResponse($patients, 'Patients retrieved successfully');
    }

    /**
     * Store a new patient.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'doctor_id' => 'required|exists:doctors,id',
            'result' => 'required|in:Healthy,Cavity,Infection',
            'date' => 'nullable|date',
        ]);

        $patient = Patient::create([
            'name' => $request->name,
            'doctor_id' => $request->doctor_id,
            'result' => $request->result,
            'date' => $request->date ?? Carbon::today()->toDateString(),
        ]);

        // Log activity
        Activity::create([
            'description' => "Patient {$patient->name} was added",
            'type' => 'patient_added'
        ]);

        return $this->successResponse($patient->load('doctor'), 'Patient added successfully');
    }

    /**
     * Delete a patient.
     */
    public function destroy($id)
    {
        $patient = Patient::find($id);

        if (!$patient) {
            return $this->errorResponse([], 'Patient not found', 404);
        }

        $name = $patient->name;
        $patient->delete();

        // Log activity
        Activity::create([
            'description' => "Patient {$name} was deleted",
            'type' => 'patient_deleted'
        ]);

        return $this->successResponse([], 'Patient deleted successfully');
    }
}
