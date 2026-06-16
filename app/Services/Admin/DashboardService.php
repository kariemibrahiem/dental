<?php

namespace App\Services\Admin;

use App\Models\Patient;
use App\Models\Doctor;
use App\Models\Activity;
use App\Models\Reservation;
use App\Support\DentalCaseCatalog;
use App\Support\ReservationStatusCatalog;
use Carbon\Carbon;

class DashboardService
{
    public function getMetrics()
    {
        $patients = Patient::with('doctor')->get();
        $reservations = Reservation::with('patient', 'doctor')->get();
        $totalPatients = $patients->count();
        $totalDoctors = Doctor::count();
        $totalReservations = $reservations->count();

        $healthyCount = $patients->filter(
            fn ($patient) => DentalCaseCatalog::normalize($patient->result) === DentalCaseCatalog::HEALTHY
        )->count();
        $cavityCount = $patients->filter(
            fn ($patient) => DentalCaseCatalog::normalize($patient->result) === DentalCaseCatalog::CAVITY
        )->count();
        $infectionCount = $patients->filter(
            fn ($patient) => DentalCaseCatalog::normalize($patient->result) === DentalCaseCatalog::INFECTION
        )->count();

        $dailyPatientsRaw = $patients
            ->filter(fn ($patient) => !empty($patient->date))
            ->groupBy(fn ($patient) => Carbon::parse($patient->date)->format('Y-m-d'))
            ->sortKeys();

        $dailyLabels = $dailyPatientsRaw
            ->keys()
            ->map(fn ($date) => Carbon::parse($date)->format('M d'))
            ->values()
            ->all();

        $dailyData = $dailyPatientsRaw
            ->map(fn ($group) => $group->count())
            ->values()
            ->all();

        $casesLabels = DentalCaseCatalog::frontResults();
        $casesData = [$healthyCount, $cavityCount, $infectionCount];

        $patientsByDocRaw = Doctor::withCount(['patients', 'reservations', 'reports'])->get();
        $docLabels = [];
        $docData = [];
        foreach ($patientsByDocRaw as $doc) {
            $docLabels[] = $doc->name;
            $docData[] = $doc->patients_count;
        }

        $reservationStatusLabels = [
            ReservationStatusCatalog::PENDING,
            ReservationStatusCatalog::ACCEPTED,
            ReservationStatusCatalog::REFUSED,
            ReservationStatusCatalog::CANCELLED,
        ];
        $reservationStatusData = [
            $reservations->where('status', ReservationStatusCatalog::PENDING)->count(),
            $reservations->where('status', ReservationStatusCatalog::ACCEPTED)->count(),
            $reservations->where('status', ReservationStatusCatalog::REFUSED)->count(),
            $reservations->where('status', ReservationStatusCatalog::CANCELLED)->count(),
        ];

        $criticalCases = $patients
            ->filter(fn ($patient) => DentalCaseCatalog::isCritical($patient->result))
            ->sortByDesc(fn ($patient) => $patient->date ? Carbon::parse($patient->date)->timestamp : $patient->created_at?->timestamp)
            ->map(function ($patient) {
                return [
                    'id' => $patient->id,
                    'name' => $patient->name,
                    'doctor' => $patient->doctor ? $patient->doctor->name : '-',
                    'date' => $patient->date ? Carbon::parse($patient->date)->format('Y-m-d') : '-',
                    'result' => DentalCaseCatalog::normalize($patient->result),
                    'raw_result' => $patient->result,
                ];
            })
            ->values();

        $recentActivities = Activity::orderBy('created_at', 'desc')
            ->take(10)
            ->get()
            ->map(function ($act) {
                return [
                    'id' => $act->id,
                    'description' => $act->description,
                    'type' => $act->type,
                    'time' => Carbon::parse($act->created_at)->diffForHumans(),
                ];
            });

        $recentReservations = $reservations
            ->sortByDesc('created_at')
            ->take(10)
            ->map(function ($reservation) {
                return [
                    'id' => $reservation->id,
                    'patient_name' => $reservation->patient?->name,
                    'doctor_name' => $reservation->doctor?->name,
                    'title' => $reservation->title,
                    'status' => $reservation->status,
                    'reservation_time' => $reservation->reservation_time?->toDateTimeString(),
                    'created_at' => $reservation->created_at?->toDateTimeString(),
                ];
            })
            ->values();

        $doctorStatistics = Doctor::withCount(['patients', 'reservations', 'reports'])
            ->orderByDesc('patients_count')
            ->orderBy('name')
            ->get()
            ->map(function ($doctor) {
                return [
                    'id' => $doctor->id,
                    'name' => $doctor->name,
                    'patients_count' => $doctor->patients_count,
                    'reservations_count' => $doctor->reservations_count,
                    'reports_count' => $doctor->reports_count,
                ];
            })
            ->values();

        return [
            'stats' => [
                'total_patients' => $totalPatients,
                'total_doctors' => $totalDoctors,
                'total_reservations' => $totalReservations,
                'healthy' => $healthyCount,
                'cavity' => $cavityCount,
                'infection' => $infectionCount,
                'pending_reservations' => $reservationStatusData[0],
                'accepted_reservations' => $reservationStatusData[1],
                'refused_reservations' => $reservationStatusData[2],
                'cancelled_reservations' => $reservationStatusData[3],
            ],
            'total_patients' => $totalPatients,
            'total_doctors' => $totalDoctors,
            'total_reservations' => $totalReservations,
            'healthy_count' => $healthyCount,
            'cavity_count' => $cavityCount,
            'infection_count' => $infectionCount,
            'pending_reservations' => $reservationStatusData[0],
            'accepted_reservations' => $reservationStatusData[1],
            'refused_reservations' => $reservationStatusData[2],
            'cancelled_reservations' => $reservationStatusData[3],
            'charts' => [
                'daily_patients' => [
                    'labels' => $dailyLabels,
                    'data' => $dailyData,
                ],
                'cases_distribution' => [
                    'labels' => $casesLabels,
                    'data' => $casesData,
                ],
                'patients_by_doctor' => [
                    'labels' => $docLabels,
                    'data' => $docData,
                ],
                'reservations_by_status' => [
                    'labels' => $reservationStatusLabels,
                    'data' => $reservationStatusData,
                ],
            ],
            'alerts' => $criticalCases,
            'recent_activity' => $recentActivities,
            'recent_activities' => $recentActivities,
            'recent_reservations' => $recentReservations,
            'doctor_statistics' => $doctorStatistics,
            'case_results' => DentalCaseCatalog::frontResults(),
        ];
    }
}
