<?php

use App\Http\Controllers\v1\ClinicAuthController;
use App\Http\Controllers\v1\ClinicUserController;
use App\Http\Controllers\v1\DashboardController;
use App\Http\Controllers\v1\DoctorAccountController;
use App\Http\Controllers\v1\DoctorController;
use App\Http\Controllers\v1\DoctorAuthController;
use App\Http\Controllers\v1\DoctorReportController;
use App\Http\Controllers\v1\DoctorReservationController;
use App\Http\Controllers\v1\PatientAuthController;
use App\Http\Controllers\v1\PatientAccountController;
use App\Http\Controllers\v1\PatientDashboardController;
use App\Http\Controllers\v1\DoctorDashboardController;
use App\Http\Controllers\v1\PatientController;
use App\Http\Controllers\v1\PatientReservationController;
use App\Http\Controllers\v1\ReportController;
use App\Support\DentalCaseCatalog;
use App\Support\ReservationStatusCatalog;
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
    Route::get('doctors/list', [DoctorAuthController::class, 'directory']);
    Route::get('lookups/case-results', function () {
        return response()->json([
            'status' => 'success',
            'message' => 'Case results retrieved successfully',
            'data' => DentalCaseCatalog::frontResults(),
        ]);
    });
    Route::get('lookups/reservation-statuses', function () {
        return response()->json([
            'status' => 'success',
            'message' => 'Reservation statuses retrieved successfully',
            'data' => ReservationStatusCatalog::all(),
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
        
        // --------------------------------------
        // Patient Guard Routes
        // --------------------------------------
        Route::group(['prefix' => 'patient'], function() {
            Route::get('dashboard', [PatientDashboardController::class, 'index']);
            Route::get('statistics', [PatientAccountController::class, 'statistics']);
            Route::post('logout', [PatientAccountController::class, 'logout']);
            Route::get('doctors', [DoctorAuthController::class, 'directory']);
            Route::post('scans/upload', [PatientDashboardController::class, 'uploadScan']);
            Route::get('profile', [PatientAccountController::class, 'show']);
            Route::put('profile', [PatientAccountController::class, 'update']);
            Route::patch('profile', [PatientAccountController::class, 'update']);
            Route::put('password', [PatientAccountController::class, 'updatePassword']);
            Route::patch('password', [PatientAccountController::class, 'updatePassword']);
            Route::delete('account', [PatientAccountController::class, 'destroy']);
            
            // Medical Reports
            Route::get('reports', [ReportController::class, 'index']);
            Route::post('reports', [ReportController::class, 'store']);
            Route::get('reports/{id}', [ReportController::class, 'show']);

            // Reservations
            Route::get('reservations', [PatientReservationController::class, 'index']);
            Route::post('reservations', [PatientReservationController::class, 'store']);
            Route::get('reservations/{id}', [PatientReservationController::class, 'show']);
            Route::put('reservations/{id}', [PatientReservationController::class, 'update']);
            Route::patch('reservations/{id}', [PatientReservationController::class, 'update']);
            Route::delete('reservations/{id}', [PatientReservationController::class, 'destroy']);
        });

        // --------------------------------------
        // Doctor Guard Routes
        // --------------------------------------
        Route::group(['prefix' => 'doctor'], function() {
            Route::get('dashboard', [DoctorDashboardController::class, 'index']);
            Route::get('statistics', [DoctorAccountController::class, 'statistics']);
            Route::post('logout', [DoctorAccountController::class, 'logout']);
            Route::post('scans/{id}/review', [DoctorDashboardController::class, 'reviewScan']);
            Route::get('profile', [DoctorAccountController::class, 'show']);
            Route::put('profile', [DoctorAccountController::class, 'update']);
            Route::patch('profile', [DoctorAccountController::class, 'update']);
            Route::put('password', [DoctorAccountController::class, 'updatePassword']);
            Route::patch('password', [DoctorAccountController::class, 'updatePassword']);
            Route::delete('account', [DoctorAccountController::class, 'destroy']);
            Route::get('patients', [DoctorAccountController::class, 'patients']);
            Route::get('reports', [DoctorReportController::class, 'index']);
            Route::get('reports/{id}', [DoctorReportController::class, 'show']);
            Route::get('reservations', [DoctorReservationController::class, 'index']);
            Route::get('reservations/{id}', [DoctorReservationController::class, 'show']);
            Route::post('reservations/{id}/accept', [DoctorReservationController::class, 'accept']);
            Route::post('reservations/{id}/refuse', [DoctorReservationController::class, 'refuse']);
            Route::put('reservations/{id}/time', [DoctorReservationController::class, 'updateTime']);
            Route::patch('reservations/{id}/time', [DoctorReservationController::class, 'updateTime']);
        });
    });
});
