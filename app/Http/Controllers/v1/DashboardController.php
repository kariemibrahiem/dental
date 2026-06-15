<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiTrait;
use App\Http\Traits\RequiresClinicUser;
use App\Services\Admin\DashboardService;
use App\Models\User;

class DashboardController extends Controller
{
    use ApiTrait, RequiresClinicUser;

    protected DashboardService $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    /**
     * Get complete dashboard metrics, charts data, activities, and alerts.
     */
    public function index()
    {
        $actor = $this->requireClinicUser();
        if (!$actor instanceof User) {
            return $actor;
        }

        try {
            $metrics = $this->dashboardService->getMetrics();
            return $this->successResponse($metrics, 'Dashboard metrics retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse([], 'Failed to retrieve dashboard metrics: ' . $e->getMessage(), 500);
        }
    }
}
