<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiTrait;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ClinicAuthController extends Controller
{
    use ApiTrait;

    public function login(Request $request)
    {
        $request->validate([
            'password' => 'required|string|min:6',
        ]);

        $login = $request->input('login')
            ?? $request->input('username')
            ?? $request->input('email')
            ?? $request->input('phone');

        if (!$login) {
            return $this->errorResponse([], 'The login field is required', 422);
        }

        $user = User::where(function ($query) use ($login) {
            $query->where('email', $login)
                ->orWhere('phone', $login)
                ->orWhere('name', $login);
        })->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return $this->errorResponse([], 'Invalid credentials', 401);
        }

        if (!(bool) $user->status) {
            return $this->errorResponse([], 'This user account is inactive', 403);
        }

        $token = $user->createToken('Clinic API Token')->plainTextToken;

        return $this->successResponse([
            'user' => $this->serializeUser($user),
            'token' => $token,
        ], 'Login successful');
    }

    public function me(Request $request)
    {
        $user = $request->user();

        if (!$user instanceof User) {
            return $this->errorResponse([], 'Unauthorized or not a clinic user', 401);
        }

        return $this->successResponse(
            $this->serializeUser($user),
            'Authenticated user retrieved successfully'
        );
    }

    public function logout(Request $request)
    {
        $user = $request->user();

        if (!$user instanceof User || !$user->currentAccessToken()) {
            return $this->errorResponse([], 'Unauthorized or not a clinic user', 401);
        }

        $user->currentAccessToken()->delete();

        return $this->successResponse([], 'Logout successful');
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
