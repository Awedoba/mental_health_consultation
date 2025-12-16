<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public routes
Route::prefix('auth')->group(function () {
    Route::post('/login', [\App\Http\Controllers\Api\AuthController::class, 'login']);
    Route::post('/password/reset', [\App\Http\Controllers\Api\AuthController::class, 'requestPasswordReset']);
});

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::get('/me', [\App\Http\Controllers\Api\AuthController::class, 'me']);
        Route::post('/logout', [\App\Http\Controllers\Api\AuthController::class, 'logout']);
        Route::post('/password/change', [\App\Http\Controllers\Api\AuthController::class, 'changePassword']);
    });

    // Admin routes
    Route::prefix('admin')->middleware('role:admin')->group(function () {
        Route::apiResource('users', \App\Http\Controllers\Api\Admin\UserController::class);
        Route::post('users/{user}/password', [\App\Http\Controllers\Api\Admin\UserController::class, 'changePassword']);
        Route::post('users/{user}/reset-password', [\App\Http\Controllers\Api\Admin\UserController::class, 'resetPassword']);
        Route::post('users/{user}/unlock', [\App\Http\Controllers\Api\Admin\UserController::class, 'unlock']);
        Route::get('users/{user}/activity', [\App\Http\Controllers\Api\Admin\UserController::class, 'activity']);
    });

    // Patient routes
    Route::apiResource('patients', \App\Http\Controllers\Api\PatientController::class);
    
    // Consultation routes
    Route::apiResource('consultations', \App\Http\Controllers\Api\ConsultationController::class);
    
    // Consultation sub-resources
    Route::prefix('consultations/{consultation}')->group(function () {
        Route::post('/mse', [\App\Http\Controllers\Api\MentalStateExamController::class, 'store']);
        Route::get('/mse', [\App\Http\Controllers\Api\MentalStateExamController::class, 'show']);
        Route::apiResource('diagnoses', \App\Http\Controllers\Api\DiagnosisController::class);
        Route::get('/management-plan', [\App\Http\Controllers\Api\ManagementPlanController::class, 'show']);
        Route::post('/management-plan', [\App\Http\Controllers\Api\ManagementPlanController::class, 'store']);
        Route::apiResource('reviews', \App\Http\Controllers\Api\ConsultationReviewController::class);
    });
    
    // Reports and Dashboard
    Route::prefix('reports')->group(function () {
        Route::get('/patients', [\App\Http\Controllers\Api\ReportController::class, 'patients']);
        Route::get('/consultations', [\App\Http\Controllers\Api\ReportController::class, 'consultations']);
        Route::get('/diagnoses', [\App\Http\Controllers\Api\ReportController::class, 'diagnoses']);
        Route::get('/quality', [\App\Http\Controllers\Api\ReportController::class, 'quality']);
    });
    
    Route::get('/dashboard', [\App\Http\Controllers\Api\DashboardController::class, 'index']);
    
    // Protected API routes will be added here
});
