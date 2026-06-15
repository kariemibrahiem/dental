<?php

namespace App\Services\Admin;

use App\Models\Patient;
use App\Models\Doctor;
use App\Models\Activity;
use App\Support\DentalCaseCatalog;
use Carbon\Carbon;

class DashboardService
{
    public function getMetrics()
    {
        $patients = Patient::with('doctor')->get();
        $totalPatients = $patients->count();
        $totalDoctors = Doctor::count();

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

        $patientsByDocRaw = Doctor::withCount('patients')->get();
        $docLabels = [];
        $docData = [];
        foreach ($patientsByDocRaw as $doc) {
            $docLabels[] = $doc->name;
            $docData[] = $doc->patients_count;
        }

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

        $doctorStatistics = Doctor::withCount('patients')
            ->orderByDesc('patients_count')
            ->orderBy('name')
            ->get()
            ->map(function ($doctor) {
                return [
                    'id' => $doctor->id,
                    'name' => $doctor->name,
                    'patients_count' => $doctor->patients_count,
                ];
            })
            ->values();

        return [
            'stats' => [
                'total_patients' => $totalPatients,
                'total_doctors' => $totalDoctors,
                'healthy' => $healthyCount,
                'cavity' => $cavityCount,
                'infection' => $infectionCount,
            ],
            'total_patients' => $totalPatients,
            'total_doctors' => $totalDoctors,
            'healthy_count' => $healthyCount,
            'cavity_count' => $cavityCount,
            'infection_count' => $infectionCount,
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
            ],
            'alerts' => $criticalCases,
            'recent_activity' => $recentActivities,
            'recent_activities' => $recentActivities,
            'doctor_statistics' => $doctorStatistics,
            'case_results' => DentalCaseCatalog::frontResults(),
        ];
    }
}
