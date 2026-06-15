<?php

namespace App\Http\Traits;

use App\Models\User;
use Illuminate\Http\JsonResponse;

trait RequiresClinicUser
{
    protected function requireClinicUser(): User|JsonResponse
    {
        $actor = auth()->user();

        if (!$actor instanceof User) {
            return $this->errorResponse([], 'Unauthorized or not a clinic user', 401);
        }

        return $actor;
    }
}
