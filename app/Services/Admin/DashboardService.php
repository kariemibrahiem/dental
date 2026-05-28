<?php

namespace App\Services\Admin;

use App\Models\Patient;
use App\Models\Doctor;
use App\Models\Activity;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    public function getMetrics()
    {
        $totalPatients = Patient::count();
        $totalDoctors = Doctor::count();
        
        $healthyCount = Patient::where('result', 'Healthy')->count();
        $calculusCount = Patient::where('result', 'Calculus')->count();
        $cariesCount = Patient::where('result', 'Caries')->count();
        $gingivitisCount = Patient::where('result', 'Gingivitis')->count();
        $hypodontiaCount = Patient::where('result', 'Hypodontia')->count();
        $discolorationCount = Patient::where('result', 'Tooth Discoloration')->count();
        $ulcersCount = Patient::where('result', 'Ulcers')->count();

        // General disease counts for KPI badge alerts
        $infectionCount = Patient::whereIn('result', ['Calculus', 'Caries', 'Gingivitis', 'Hypodontia', 'Tooth Discoloration', 'Ulcers'])->count();

        // 1. Daily Patients (Last 7 days of entries)
        $dailyPatientsRaw = Patient::select('date', DB::raw('count(*) as count'))
            ->whereNotNull('date')
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        $dailyLabels = [];
        $dailyData = [];
        foreach ($dailyPatientsRaw as $dp) {
            $dailyLabels[] = Carbon::parse($dp->date)->format('M d');
            $dailyData[] = (int) $dp->count;
        }

        // 2. Cases Distribution (Supports all 6 diseases + Healthy)
        $casesLabels = ['Healthy', 'Calculus', 'Caries', 'Gingivitis', 'Hypodontia', 'Discoloration', 'Ulcers'];
        $casesData = [
            $healthyCount, 
            $calculusCount, 
            $cariesCount, 
            $gingivitisCount, 
            $hypodontiaCount, 
            $discolorationCount, 
            $ulcersCount
        ];

        // 3. Patients by Doctor
        $patientsByDocRaw = Doctor::withCount('patients')->get();
        $docLabels = [];
        $docData = [];
        foreach ($patientsByDocRaw as $doc) {
            $docLabels[] = $doc->name;
            $docData[] = $doc->patients_count;
        }

        // 4. Alerts (Critical cases = any active disease other than Healthy)
        $criticalCases = Patient::with('doctor')
            ->whereIn('result', ['Calculus', 'Caries', 'Gingivitis', 'Hypodontia', 'Tooth Discoloration', 'Ulcers'])
            ->orderBy('date', 'desc')
            ->get()
            ->map(function ($patient) {
                return [
                    'id' => $patient->id,
                    'name' => $patient->name,
                    'doctor' => $patient->doctor ? $patient->doctor->name : '-',
                    'date' => $patient->date ? Carbon::parse($patient->date)->format('Y-m-d') : '-',
                    'result' => $patient->result,
                ];
            });

        // 5. Recent Activity
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

        return [
            'total_patients' => $totalPatients,
            'total_doctors' => $totalDoctors,
            'healthy_count' => $healthyCount,
            'calculus_count' => $calculusCount,
            'caries_count' => $cariesCount,
            'gingivitis_count' => $gingivitisCount,
            'hypodontia_count' => $hypodontiaCount,
            'discoloration_count' => $discolorationCount,
            'ulcers_count' => $ulcersCount,
            'infection_count' => $infectionCount, // general critical alerts
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
            'recent_activities' => $recentActivities,
        ];
    }
}
