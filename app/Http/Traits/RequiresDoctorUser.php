<?php

namespace App\Http\Traits;

use App\Models\Doctor;
use Illuminate\Http\JsonResponse;

trait RequiresDoctorUser
{
    protected function requireDoctor(): Doctor|JsonResponse
    {
        $actor = auth()->user();

        if (!$actor instanceof Doctor) {
            return $this->errorResponse([], 'Unauthorized or not a doctor', 401);
        }

        return $actor;
    }
}
