<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiTrait;
use App\Http\Traits\RequiresDoctorUser;
use App\Http\Traits\SerializesDentalApiData;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Activity;
use App\Models\Report;
use App\Models\Reservation;
use App\Models\Scan;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Support\DentalCaseCatalog;
use App\Support\ReservationStatusCatalog;

class DoctorDashboardController extends Controller
{
    use ApiTrait;
    use RequiresDoctorUser;
    use SerializesDentalApiData;

    /**
     * Get doctor dashboard stats (assigned patients count, pending scan counts) and list of their patients.
     */
    public function index()
    {
        $doctor = $this->requireDoctor();
        if (!$doctor instanceof Doctor) {
            return $doctor;
        }

        // Assigned patients count
        $patientsCount = Patient::where('doctor_id', $doctor->id)->count();

        // Pending scans to review count
        $pendingScansCount = Scan::where('doctor_id', $doctor->id)
            ->where('status', 'pending')
            ->count();

        // List of patients assigned to this doctor
        $patients = Patient::where('doctor_id', $doctor->id)
            ->orderBy('name')
            ->get();

        // Pending scans lists
        $pendingScans = Scan::with('patient')
            ->where('doctor_id', $doctor->id)
            ->where('status', 'pending')
            ->latest()
            ->get();

        $reports = Report::with('patient', 'doctor')
            ->where('doctor_id', $doctor->id)
            ->latest()
            ->get();

        $reservations = Reservation::with('patient', 'doctor')
            ->where('doctor_id', $doctor->id)
            ->latest()
            ->get();

        return $this->successResponse([
            'stats' => [
                'assigned_patients' => $patientsCount,
                'pending_reviews' => $pendingScansCount,
                'total_reports' => $reports->count(),
                'total_reservations' => $reservations->count(),
                'pending_reservations' => $reservations->where('status', ReservationStatusCatalog::PENDING)->count(),
                'accepted_reservations' => $reservations->where('status', ReservationStatusCatalog::ACCEPTED)->count(),
                'refused_reservations' => $reservations->where('status', ReservationStatusCatalog::REFUSED)->count(),
                'cancelled_reservations' => $reservations->where('status', ReservationStatusCatalog::CANCELLED)->count(),
            ],
            'patients' => $patients->map(fn (Patient $patient) => $this->serializePatient($patient->load('doctor.specialties')))->values(),
            'pending_scans' => $pendingScans,
            'reports' => $reports->map(fn (Report $report) => $this->serializeReport($report))->values(),
            'reservations' => $reservations->map(fn (Reservation $reservation) => $this->serializeReservation($reservation))->values(),
        ], 'Doctor dashboard data retrieved successfully');
    }

    /**
     * Doctor reviews a patient's scan, appends clinical notes, and updates scan status to reviewed.
     */
    public function reviewScan(Request $request, $id)
    {
        $doctor = $this->requireDoctor();
        if (!$doctor instanceof Doctor) {
            return $doctor;
        }

        $request->validate([
            'notes' => 'required|string|min:5',
            'override_result' => ['nullable', Rule::in(DentalCaseCatalog::allResults())],
        ]);

        $scan = Scan::where('id', $id)->where('doctor_id', $doctor->id)->first();

        if (!$scan) {
            return $this->errorResponse([], 'Scan not found or not assigned to this doctor', 404);
        }

        try {
            $updateData = [
                'notes' => $request->notes,
                'status' => 'reviewed',
            ];

            if ($request->filled('override_result')) {
                $updateData['ai_result'] = $request->override_result;
                
                // Also update patient primary result cache
                if ($scan->patient) {
                    $scan->patient->update([
                        'result' => DentalCaseCatalog::normalize($request->override_result)
                    ]);
                }
            }

            $scan->update($updateData);

            // Log activity
            Activity::create([
                'description' => "Doctor {$doctor->name} reviewed scan for Patient " . ($scan->patient ? $scan->patient->name : 'Unknown'),
                'type' => 'doctor_added'
            ]);

            return $this->successResponse($scan->load('patient'), 'Scan reviewed successfully by doctor');
        } catch (\Exception $e) {
            return $this->errorResponse([], 'Failed to submit scan review: ' . $e->getMessage(), 500);
        }
    }
}
