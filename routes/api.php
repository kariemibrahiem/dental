<?php

use App\Http\Controllers\v1\ClinicAuthController;
use App\Http\Controllers\v1\ClinicUserController;
use App\Http\Controllers\v1\DashboardController;
use App\Http\Controllers\v1\DoctorController;
use App\Http\Controllers\v1\DoctorAuthController;
use App\Http\Controllers\v1\PatientAuthController;
use App\Http\Controllers\v1\PatientDashboardController;
use App\Http\Controllers\v1\DoctorDashboardController;
use App\Http\Controllers\v1\PatientController;
use App\Http\Controllers\v1\ReportController;
use App\Support\DentalCaseCatalog;
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
    Route::post('auth/login', [ClinicAuthController::class, 'login']);
    Route::post('auth/doctor/login', [DoctorAuthController::class, 'login']);
    Route::post('auth/patient/login', [PatientAuthController::class, 'login']);
    Route::post('auth/patient/register', [PatientAuthController::class, 'register']);
    
    Route::get('specialties', [DoctorAuthController::class, 'specialties']);
    Route::get('lookups/case-results', function () {
        return response()->json([
            'status' => 'success',
            'message' => 'Case results retrieved successfully',
            'data' => DentalCaseCatalog::frontResults(),
        ]);
    });

    // ==========================================
    // Protected Multi-Guard API Routes
    // ==========================================
    Route::group(['middleware' => 'auth:sanctum'], function() {
        Route::post('auth/logout', [ClinicAuthController::class, 'logout']);
        Route::get('auth/me', [ClinicAuthController::class, 'me']);
        Route::get('dashboard', [DashboardController::class, 'index']);
        Route::apiResource('doctors', DoctorController::class);
        Route::apiResource('patients', PatientController::class);
        Route::apiResource('users', ClinicUserController::class);
        
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
