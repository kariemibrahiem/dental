<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiTrait;
use App\Http\Traits\RequiresClinicUser;
use App\Models\Doctor;
use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Support\IdentityGenerator;
use App\Models\User;

class DoctorController extends Controller
{
    use ApiTrait, RequiresClinicUser;

    /**
     * Get all doctors.
     */
    public function index()
    {
        $actor = $this->requireClinicUser();
        if (!$actor instanceof User) {
            return $actor;
        }

        $doctors = Doctor::with('specialties')
            ->withCount('patients')
            ->orderBy('name')
            ->get()
            ->map(fn (Doctor $doctor) => $this->serializeDoctor($doctor))
            ->values();

        return $this->successResponse($doctors, 'Doctors retrieved successfully');
    }

    /**
     * Store a new doctor.
     */
    public function store(Request $request)
    {
        $actor = $this->requireClinicUser();
        if (!$actor instanceof User) {
            return $actor;
        }

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['nullable', 'email', Rule::unique('doctors', 'email')],
            'phone' => 'nullable|string|max:30',
            'password' => 'nullable|string|min:6',
            'specialty_ids' => 'nullable|array',
            'specialty_ids.*' => 'integer|exists:specialties,id',
        ]);

        $generatedEmail = $data['email'] ?? IdentityGenerator::uniqueEmail(Doctor::class, $data['name'], 'doctor');
        $generatedPassword = $data['password'] ?? IdentityGenerator::temporaryPassword();

        $doctor = Doctor::create([
            'name' => $data['name'],
            'email' => $generatedEmail,
            'phone' => $data['phone'] ?? null,
            'password' => $generatedPassword,
        ]);

        if (array_key_exists('specialty_ids', $data)) {
            $doctor->specialties()->sync($data['specialty_ids'] ?? []);
        }

        // Log activity
        Activity::create([
            'description' => "Doctor {$doctor->name} was added",
            'type' => 'doctor_added'
        ]);

        return $this->successResponse([
            'doctor' => $this->serializeDoctor($doctor->fresh()->load('specialties')->loadCount('patients')),
            'generated_credentials' => $this->generatedCredentialsPayload($data, $generatedEmail, $generatedPassword),
        ], 'Doctor added successfully');
    }

    /**
     * Show a single doctor.
     */
    public function show(string $id)
    {
        $actor = $this->requireClinicUser();
        if (!$actor instanceof User) {
            return $actor;
        }

        $doctor = Doctor::with('specialties')->withCount('patients')->find($id);

        if (!$doctor) {
            return $this->errorResponse([], 'Doctor not found', 404);
        }

        return $this->successResponse(
            $this->serializeDoctor($doctor),
            'Doctor retrieved successfully'
        );
    }

    /**
     * Update an existing doctor.
     */
    public function update(Request $request, string $id)
    {
        $actor = $this->requireClinicUser();
        if (!$actor instanceof User) {
            return $actor;
        }

        $doctor = Doctor::find($id);

        if (!$doctor) {
            return $this->errorResponse([], 'Doctor not found', 404);
        }

        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => ['sometimes', 'email', Rule::unique('doctors', 'email')->ignore($doctor->id)],
            'phone' => 'sometimes|string|max:30',
            'password' => 'nullable|string|min:6',
            'specialty_ids' => 'sometimes|array',
            'specialty_ids.*' => 'integer|exists:specialties,id',
        ]);

        $updateData = [];

        foreach (['name', 'email', 'phone'] as $field) {
            if (array_key_exists($field, $data)) {
                $updateData[$field] = $data[$field];
            }
        }

        if (!empty($data['password'])) {
            $updateData['password'] = $data['password'];
        }

        if ($updateData !== []) {
            $doctor->update($updateData);
        }

        if (array_key_exists('specialty_ids', $data)) {
            $doctor->specialties()->sync($data['specialty_ids'] ?? []);
        }

        Activity::create([
            'description' => "Doctor {$doctor->name} was updated",
            'type' => 'doctor_updated'
        ]);

        return $this->successResponse(
            $this->serializeDoctor($doctor->fresh()->load('specialties')->loadCount('patients')),
            'Doctor updated successfully'
        );
    }

    /**
     * Delete a doctor.
     */
    public function destroy($id)
    {
        $actor = $this->requireClinicUser();
        if (!$actor instanceof User) {
            return $actor;
        }

        $doctor = Doctor::find($id);
        
        if (!$doctor) {
            return $this->errorResponse([], 'Doctor not found', 404);
        }

        $name = $doctor->name;
        $doctor->delete();

        // Log activity
        Activity::create([
            'description' => "Doctor {$name} was deleted",
            'type' => 'doctor_deleted'
        ]);

        return $this->successResponse([], 'Doctor deleted successfully');
    }

    private function serializeDoctor(Doctor $doctor): array
    {
        return [
            'id' => $doctor->id,
            'name' => $doctor->name,
            'email' => $doctor->email,
            'phone' => $doctor->phone,
            'patients_count' => $doctor->patients_count ?? $doctor->patients()->count(),
            'specialties' => $doctor->specialties->map(fn ($specialty) => [
                'id' => $specialty->id,
                'name' => $specialty->name,
            ])->values(),
            'created_at' => $doctor->created_at?->toDateTimeString(),
            'updated_at' => $doctor->updated_at?->toDateTimeString(),
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
