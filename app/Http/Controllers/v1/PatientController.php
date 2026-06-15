<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiTrait;
use App\Http\Traits\RequiresClinicUser;
use App\Models\Patient;
use App\Models\Activity;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use App\Support\DentalCaseCatalog;
use App\Support\IdentityGenerator;
use App\Models\User;

class PatientController extends Controller
{
    use ApiTrait, RequiresClinicUser;

    /**
     * Get all patients with their doctors.
     */
    public function index()
    {
        $actor = $this->requireClinicUser();
        if (!$actor instanceof User) {
            return $actor;
        }

        $patients = Patient::with('doctor')
            ->latest()
            ->get()
            ->map(fn (Patient $patient) => $this->serializePatient($patient))
            ->values();

        return $this->successResponse($patients, 'Patients retrieved successfully');
    }

    /**
     * Store a new patient.
     */
    public function store(Request $request)
    {
        $actor = $this->requireClinicUser();
        if (!$actor instanceof User) {
            return $actor;
        }

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'doctor_id' => 'required|exists:doctors,id',
            'result' => ['nullable', Rule::in(DentalCaseCatalog::allResults())],
            'date' => 'nullable|date',
            'email' => ['nullable', 'email', Rule::unique('patients', 'email')],
            'phone' => 'nullable|string|max:30',
            'password' => 'nullable|string|min:6',
        ]);

        $generatedEmail = $data['email'] ?? IdentityGenerator::uniqueEmail(Patient::class, $data['name'], 'patient');
        $generatedPassword = $data['password'] ?? IdentityGenerator::temporaryPassword();
        $normalizedResult = DentalCaseCatalog::normalize($data['result'] ?? DentalCaseCatalog::HEALTHY);

        $patient = Patient::create([
            'name' => $data['name'],
            'email' => $generatedEmail,
            'phone' => $data['phone'] ?? null,
            'password' => $generatedPassword,
            'doctor_id' => $data['doctor_id'],
            'result' => $normalizedResult,
            'date' => $data['date'] ?? Carbon::today()->toDateString(),
        ]);

        // Log activity
        Activity::create([
            'description' => "Patient {$patient->name} was added",
            'type' => 'patient_added'
        ]);

        return $this->successResponse([
            'patient' => $this->serializePatient($patient->fresh()->load('doctor')),
            'generated_credentials' => $this->generatedCredentialsPayload($data, $generatedEmail, $generatedPassword),
        ], 'Patient added successfully');
    }

    /**
     * Show a single patient.
     */
    public function show(string $id)
    {
        $actor = $this->requireClinicUser();
        if (!$actor instanceof User) {
            return $actor;
        }

        $patient = Patient::with('doctor')->find($id);

        if (!$patient) {
            return $this->errorResponse([], 'Patient not found', 404);
        }

        return $this->successResponse(
            $this->serializePatient($patient),
            'Patient retrieved successfully'
        );
    }

    /**
     * Update an existing patient.
     */
    public function update(Request $request, string $id)
    {
        $actor = $this->requireClinicUser();
        if (!$actor instanceof User) {
            return $actor;
        }

        $patient = Patient::find($id);

        if (!$patient) {
            return $this->errorResponse([], 'Patient not found', 404);
        }

        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'doctor_id' => 'sometimes|exists:doctors,id',
            'result' => ['nullable', Rule::in(DentalCaseCatalog::allResults())],
            'date' => 'sometimes|nullable|date',
            'email' => ['sometimes', 'email', Rule::unique('patients', 'email')->ignore($patient->id)],
            'phone' => 'sometimes|string|max:30',
            'password' => 'nullable|string|min:6',
        ]);

        $updateData = [];

        foreach (['name', 'doctor_id', 'email', 'phone', 'date'] as $field) {
            if (array_key_exists($field, $data)) {
                $updateData[$field] = $data[$field];
            }
        }

        if (array_key_exists('result', $data)) {
            $updateData['result'] = DentalCaseCatalog::normalize($data['result']);
        }

        if (!empty($data['password'])) {
            $updateData['password'] = $data['password'];
        }

        if ($updateData !== []) {
            $patient->update($updateData);
        }

        Activity::create([
            'description' => "Patient {$patient->name} was updated",
            'type' => 'patient_updated'
        ]);

        return $this->successResponse(
            $this->serializePatient($patient->fresh()->load('doctor')),
            'Patient updated successfully'
        );
    }

    /**
     * Delete a patient.
     */
    public function destroy($id)
    {
        $actor = $this->requireClinicUser();
        if (!$actor instanceof User) {
            return $actor;
        }

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

    private function serializePatient(Patient $patient): array
    {
        return [
            'id' => $patient->id,
            'name' => $patient->name,
            'email' => $patient->email,
            'phone' => $patient->phone,
            'doctor_id' => $patient->doctor_id,
            'doctor_name' => $patient->doctor?->name,
            'doctor' => $patient->doctor ? [
                'id' => $patient->doctor->id,
                'name' => $patient->doctor->name,
            ] : null,
            'result' => DentalCaseCatalog::normalize($patient->result),
            'raw_result' => $patient->result,
            'date' => $patient->date?->format('Y-m-d'),
            'created_at' => $patient->created_at?->toDateTimeString(),
            'updated_at' => $patient->updated_at?->toDateTimeString(),
        ];
    }

    private function generatedCredentialsPayload(array $data, string $email, string $password): ?array
    {
        $payload = [];

        if (empty($data['email'])) {
            $payload['email'] = $email;
        }

        if (empty($data['password'])) {
            $payload['temporary_password'] = $password;
        }

        return $payload !== [] ? $payload : null;
    }
}
