<?php

use App\Http\Controllers\v1\DoctorAuthController;
use App\Http\Controllers\v1\PatientAuthController;
use App\Http\Controllers\v1\PatientDashboardController;
use App\Http\Controllers\v1\DoctorDashboardController;
use App\Http\Controllers\v1\ReportController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::group(['prefix' => 'v1'], function() {
    
    // ==========================================
    // Public / Authentication Routes
    // ==========================================
    Route::post('auth/doctor/login', [DoctorAuthController::class, 'login']);
    Route::post('auth/patient/login', [PatientAuthController::class, 'login']);
    Route::post('auth/patient/register', [PatientAuthController::class, 'register']);
    
    Route::get('specialties', [DoctorAuthController::class, 'specialties']);

    // ==========================================
    // Protected Multi-Guard API Routes
    // ==========================================
    Route::group(['middleware' => 'auth:sanctum'], function() {
        
        // --------------------------------------
        // Patient Guard Routes
        // --------------------------------------
        Route::group(['prefix' => 'patient'], function() {
            Route::get('dashboard', [PatientDashboardController::class, 'index']);
            Route::post('scans/upload', [PatientDashboardController::class, 'uploadScan']);
            Route::get('profile', [PatientAuthController::class, 'profile']);
            
            // Medical Reports
            Route::get('reports', [ReportController::class, 'index']);
            Route::post('reports', [ReportController::class, 'store']);
            Route::get('reports/{id}', [ReportController::class, 'show']);
        });

        // --------------------------------------
        // Doctor Guard Routes
        // --------------------------------------
        Route::group(['prefix' => 'doctor'], function() {
            Route::get('dashboard', [DoctorDashboardController::class, 'index']);
            Route::post('scans/{id}/review', [DoctorDashboardController::class, 'reviewScan']);
            Route::get('profile', [DoctorAuthController::class, 'profile']);
        });
    });
});