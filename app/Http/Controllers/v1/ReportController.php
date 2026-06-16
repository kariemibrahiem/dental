<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiTrait;
use App\Http\Traits\RequiresPatientUser;
use App\Http\Traits\SerializesDentalApiData;
use App\Models\Activity;
use App\Models\Patient;
use App\Models\Report;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    use ApiTrait;
    use RequiresPatientUser;
    use SerializesDentalApiData;

    /**
     * Get list of reports uploaded by the authenticated patient.
     */
    public function index()
    {
        $patient = $this->requirePatient();
        if (!$patient instanceof Patient) {
            return $patient;
        }

        $reports = Report::with('doctor', 'patient')
            ->where('patient_id', $patient->id)
            ->latest()
            ->get()
            ->map(fn (Report $report) => $this->serializeReport($report))
            ->values();

        return $this->successResponse($reports, 'Reports retrieved successfully');
    }

    /**
     * Upload a new medical report (contains title, description, image).
     */
    public function store(Request $request)
    {
        $patient = $this->requirePatient();
        if (!$patient instanceof Patient) {
            return $patient;
        }

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'doctor_id' => 'nullable|exists:doctors,id',
            'image' => 'required|image|max:4096', // Max 4MB report photo
        ]);

        try {
            $doctorId = $data['doctor_id'] ?? $patient->doctor_id;

            if (!$doctorId) {
                return $this->errorResponse([], 'doctor_id is required when the patient is not assigned to a doctor', 422);
            }

            // Upload report image to storage
            $path = $request->file('image')->store('reports', 'public');

            // Save report
            $report = Report::create([
                'patient_id' => $patient->id,
                'doctor_id' => $doctorId,
                'title' => $data['title'],
                'description' => $data['description'],
                'image_path' => $path,
            ]);

            if (!$patient->doctor_id) {
                $patient->update(['doctor_id' => $doctorId]);
            }

            Activity::create([
                'description' => "Patient {$patient->name} uploaded report #{$report->id}",
                'type' => 'patient_updated',
            ]);

            return $this->successResponse(
                $this->serializeReport($report->load('doctor', 'patient')),
                'Medical report uploaded successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse([], 'Report upload failed: ' . $e->getMessage(), 500);
        }
    }

    /**
     * View a single report.
     */
    public function show($id)
    {
        $patient = $this->requirePatient();
        if (!$patient instanceof Patient) {
            return $patient;
        }

        $report = Report::with('doctor', 'patient')
            ->where('id', $id)
            ->where('patient_id', $patient->id)
            ->first();

        if (!$report) {
            return $this->errorResponse([], 'Report not found', 404);
        }

        return $this->successResponse(
            $this->serializeReport($report),
            'Report details retrieved successfully'
        );
    }
}
