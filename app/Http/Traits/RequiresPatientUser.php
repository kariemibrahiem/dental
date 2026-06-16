<?php

namespace App\Http\Traits;

use App\Models\Patient;
use Illuminate\Http\JsonResponse;

trait RequiresPatientUser
{
    protected function requirePatient(): Patient|JsonResponse
    {
        $actor = auth()->user();

        if (!$actor instanceof Patient) {
            return $this->errorResponse([], 'Unauthorized or not a patient', 401);
        }

        return $actor;
    }
}
