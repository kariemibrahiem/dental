<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiTrait;
use App\Http\Traits\RequiresClinicUser;
use App\Models\User;
use App\Support\IdentityGenerator;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ClinicUserController extends Controller
{
    use ApiTrait, RequiresClinicUser;

    public function index()
    {
        $actor = $this->requireClinicUser();
        if (!$actor instanceof User) {
            return $actor;
        }

        $users = User::latest()->get()->map(fn (User $user) => $this->serializeUser($user))->values();

        return $this->successResponse($users, 'Users retrieved successfully');
    }

    public function store(Request $request)
    {
        $actor = $this->requireClinicUser();
        if (!$actor instanceof User) {
            return $actor;
        }

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['nullable', 'email', Rule::unique('users', 'email')],
            'phone' => ['required', 'string', 'max:30', Rule::unique('users', 'phone')],
            'password' => 'required|string|min:6',
            'status' => 'nullable|boolean',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'],
            'password' => $data['password'],
            'status' => array_key_exists('status', $data) ? (bool) $data['status'] : true,
            'code' => IdentityGenerator::uniqueCode(User::class),
        ]);

        return $this->successResponse(
            $this->serializeUser($user),
            'User created successfully'
        );
    }

    public function show(string $id)
    {
        $actor = $this->requireClinicUser();
        if (!$actor instanceof User) {
            return $actor;
        }

        $user = User::find($id);

        if (!$user) {
            return $this->errorResponse([], 'User not found', 404);
        }

        return $this->successResponse(
            $this->serializeUser($user),
            'User retrieved successfully'
        );
    }

    public function update(Request $request, string $id)
    {
        $actor = $this->requireClinicUser();
        if (!$actor instanceof User) {
            return $actor;
        }

        $user = User::find($id);

        if (!$user) {
            return $this->errorResponse([], 'User not found', 404);
        }

        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => ['nullable', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['sometimes', 'string', 'max:30', Rule::unique('users', 'phone')->ignore($user->id)],
            'password' => 'nullable|string|min:6',
            'status' => 'nullable|boolean',
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

        if (array_key_exists('status', $data)) {
            $updateData['status'] = (bool) $data['status'];
        }

        if ($updateData !== []) {
            $user->update($updateData);
        }

        return $this->successResponse(
            $this->serializeUser($user->fresh()),
            'User updated successfully'
        );
    }

    public function destroy(string $id)
    {
        $actor = $this->requireClinicUser();
        if (!$actor instanceof User) {
            return $actor;
        }

        $user = User::find($id);

        if (!$user) {
            return $this->errorResponse([], 'User not found', 404);
        }

        $user->tokens()->delete();
        $user->delete();

        return $this->successResponse([], 'User deleted successfully');
    }

    private function serializeUser(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'code' => $user->code,
            'status' => (bool) $user->status,
            'created_at' => $user->created_at?->toDateTimeString(),
            'updated_at' => $user->updated_at?->toDateTimeString(),
        ];
    }
}
