<?php

namespace App\Http\Controllers\Admin\dashboard;

use App\Http\Controllers\Controller;
use App\Services\Admin\DashboardService;
use App\Traits\WeatherTrait;

class Analytics extends Controller
{
    use WeatherTrait;

    protected DashboardService $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    public function index()
    {
        // Try to fetch weather data as fallback/extra widget (optional, keep trait usage)
        try {
            $weatherData = $this->GetWeather(30.5503, 31.0106);
        } catch (\Exception $e) {
            $weatherData = null;
        }

        // Fetch all dental analytics from DashboardService
        $metrics = $this->dashboardService->getMetrics();

        return view('content.dashboard.dashboards-analytics', array_merge($metrics, [
            'weather' => $weatherData
        ]));
    }
}
