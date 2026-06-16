<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiTrait;
use App\Http\Traits\RequiresDoctorUser;
use App\Http\Traits\SerializesDentalApiData;
use App\Models\Doctor;
use App\Models\Report;

class DoctorReportController extends Controller
{
    use ApiTrait;
    use RequiresDoctorUser;
    use SerializesDentalApiData;

    public function index()
    {
        $doctor = $this->requireDoctor();
        if (!$doctor instanceof Doctor) {
            return $doctor;
        }

        $reports = Report::with('patient', 'doctor')
            ->where('doctor_id', $doctor->id)
            ->latest()
            ->get()
            ->map(fn (Report $report) => $this->serializeReport($report))
            ->values();

        return $this->successResponse($reports, 'Doctor reports retrieved successfully');
    }

    public function show(string $id)
    {
        $doctor = $this->requireDoctor();
        if (!$doctor instanceof Doctor) {
            return $doctor;
        }

        $report = Report::with('patient', 'doctor')
            ->where('doctor_id', $doctor->id)
            ->find($id);

        if (!$report) {
            return $this->errorResponse([], 'Report not found', 404);
        }

        return $this->successResponse(
            $this->serializeReport($report),
            'Doctor report retrieved successfully'
        );
    }
}
