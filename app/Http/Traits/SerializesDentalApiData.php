<?php

namespace App\Http\Traits;

use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Report;
use App\Models\Reservation;
use App\Support\DentalCaseCatalog;

trait SerializesDentalApiData
{
    protected function serializeDoctor(Doctor $doctor): array
    {
        $specialties = $doctor->relationLoaded('specialties')
            ? $doctor->specialties
            : $doctor->specialties()->get();

        return [
            'id' => $doctor->id,
            'name' => $doctor->name,
            'email' => $doctor->email,
            'phone' => $doctor->phone,
            'patients_count' => $doctor->patients_count ?? ($doctor->relationLoaded('patients') ? $doctor->patients->count() : $doctor->patients()->count()),
            'reports_count' => $doctor->reports_count ?? null,
            'reservations_count' => $doctor->reservations_count ?? null,
            'specialties' => $specialties->map(fn ($specialty) => [
                'id' => $specialty->id,
                'name' => $specialty->name,
            ])->values(),
            'created_at' => $doctor->created_at?->toDateTimeString(),
            'updated_at' => $doctor->updated_at?->toDateTimeString(),
        ];
    }

    protected function serializePatient(Patient $patient): array
    {
        $doctor = $patient->doctor;
        $normalizedResult = DentalCaseCatalog::normalize($patient->result);

        return [
            'id' => $patient->id,
            'name' => $patient->name,
            'email' => $patient->email,
            'phone' => $patient->phone,
            'doctor_id' => $patient->doctor_id,
            'doctor_name' => $doctor?->name,
            'doctor' => $doctor ? [
                'id' => $doctor->id,
                'name' => $doctor->name,
                'email' => $doctor->email,
                'phone' => $doctor->phone,
                'specialties' => $doctor->relationLoaded('specialties')
                    ? $doctor->specialties->map(fn ($specialty) => [
                        'id' => $specialty->id,
                        'name' => $specialty->name,
                    ])->values()
                    : [],
            ] : null,
            'result' => $normalizedResult,
            'raw_result' => $patient->result,
            'reports_count' => $patient->reports_count ?? null,
            'reservations_count' => $patient->reservations_count ?? null,
            'date' => $patient->date?->format('Y-m-d'),
            'created_at' => $patient->created_at?->toDateTimeString(),
            'updated_at' => $patient->updated_at?->toDateTimeString(),
        ];
    }

    protected function serializeReport(Report $report): array
    {
        return [
            'id' => $report->id,
            'patient_id' => $report->patient_id,
            'patient_name' => $report->patient?->name,
            'doctor_id' => $report->doctor_id,
            'doctor_name' => $report->doctor?->name,
            'title' => $report->title,
            'description' => $report->description,
            'image_path' => $report->image_path,
            'created_at' => $report->created_at?->toDateTimeString(),
            'updated_at' => $report->updated_at?->toDateTimeString(),
        ];
    }

    protected function serializeReservation(Reservation $reservation): array
    {
        return [
            'id' => $reservation->id,
            'patient_id' => $reservation->patient_id,
            'patient_name' => $reservation->patient?->name,
            'doctor_id' => $reservation->doctor_id,
            'doctor_name' => $reservation->doctor?->name,
            'title' => $reservation->title,
            'description' => $reservation->description,
            'image_path' => $reservation->image_path,
            'reservation_time' => $reservation->reservation_time?->toDateTimeString(),
            'status' => $reservation->status,
            'status_notes' => $reservation->status_notes,
            'created_at' => $reservation->created_at?->toDateTimeString(),
            'updated_at' => $reservation->updated_at?->toDateTimeString(),
        ];
    }
}
