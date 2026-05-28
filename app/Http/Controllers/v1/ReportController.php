<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiTrait;
use App\Models\Patient;
use App\Models\Report;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    use ApiTrait;

    /**
     * Get list of reports uploaded by the authenticated patient.
     */
    public function index()
    {
        $patient = auth()->user();

        if (!$patient || !($patient instanceof Patient)) {
            return $this->errorResponse([], 'Unauthorized or not a patient', 401);
        }

        $reports = Report::where('patient_id', $patient->id)->latest()->get();
        return $this->successResponse($reports, 'Reports retrieved successfully');
    }

    /**
     * Upload a new medical report (contains title, description, image).
     */
    public function store(Request $request)
    {
        $patient = auth()->user();

        if (!$patient || !($patient instanceof Patient)) {
            return $this->errorResponse([], 'Unauthorized or not a patient', 401);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'required|image|max:4096', // Max 4MB report photo
        ]);

        try {
            // Upload report image to storage
            $path = $request->file('image')->store('reports', 'public');

            // Save report
            $report = Report::create([
                'patient_id' => $patient->id,
                'title' => $request->title,
                'description' => $request->description,
                'image_path' => $path,
            ]);

            return $this->successResponse($report, 'Medical report uploaded successfully');
        } catch (\Exception $e) {
            return $this->errorResponse([], 'Report upload failed: ' . $e->getMessage(), 500);
        }
    }

    /**
     * View a single report.
     */
    public function show($id)
    {
        $patient = auth()->user();

        if (!$patient || !($patient instanceof Patient)) {
            return $this->errorResponse([], 'Unauthorized or not a patient', 401);
        }

        $report = Report::where('id', $id)->where('patient_id', $patient->id)->first();

        if (!$report) {
            return $this->errorResponse([], 'Report not found', 404);
        }

        return $this->successResponse($report, 'Report details retrieved successfully');
    }
}
