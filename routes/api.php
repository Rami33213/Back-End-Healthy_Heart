<?php

use App\Http\Controllers\DiagnosisController;
use App\Http\Controllers\ECGRecordController;
use App\Http\Controllers\ExpertConsultationController;
use App\Http\Controllers\HeartRateRecordController;
use App\Http\Controllers\MedicalTestController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// ═══════════════════════════════════════════════════════
// 🔓 PUBLIC ROUTES - ما تحتاج Token
// ═══════════════════════════════════════════════════════
Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);

// ═══════════════════════════════════════════════════════
// 🔒 PROTECTED ROUTES - تحتاج Token
// ═══════════════════════════════════════════════════════
Route::middleware('auth:sanctum')->group(function () {
    
    // ─── User & Auth ───
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('/logout', [UserController::class, 'logout']);

    // ─── Profile ───
    Route::get('/profile', [ProfileController::class, 'index']);
    Route::post('/profile', [ProfileController::class, 'storeOrUpdate']);

    // ─── Heart Rate Records ───
    Route::prefix('HeartRateRecord')->group(function () {
        Route::get('/', [HeartRateRecordController::class, 'index']);         // All records
        Route::post('/', [HeartRateRecordController::class, 'store']);        // Create new
        Route::get('/latest', [HeartRateRecordController::class, 'show']);    // Latest only
        Route::delete('/{id}', [HeartRateRecordController::class, 'destroy']); // Delete
    });

    // ─── ECG Records ───
    Route::prefix('ECGRecord')->group(function () {
        Route::get('/', [ECGRecordController::class, 'index']);          // All records
        Route::post('/', [ECGRecordController::class, 'store']);         // Create new
        Route::get('/latest', [ECGRecordController::class, 'show']);     // Latest only
    });

    // ─── Medical Tests ───
    Route::prefix('MedicalTest')->group(function () {
        Route::post('/', [MedicalTestController::class, 'store']);
        Route::get('/latest', [MedicalTestController::class, 'show']);
    });

    // ─── Expert Consultation ───
    Route::prefix('ExpertConsultation')->group(function () {
        Route::get('/', [ExpertConsultationController::class, 'index']);      // All records
        Route::post('/', [ExpertConsultationController::class, 'store']);     // Create new
        Route::get('/latest', [ExpertConsultationController::class, 'show']); // Latest only
    });

    // ─── Diagnosis ───
    Route::get('/Diagnosis', [DiagnosisController::class, 'show']);
    Route::post('/Diagnosis', [DiagnosisController::class, 'store']);

    // ─── Reports ───
    Route::get('/Report', [ReportController::class, 'show']);
    Route::post('/Report', [ReportController::class, 'store']);

    // ─── Settings ───
    Route::get('/settings', [SettingsController::class, 'show']);
    Route::post('/settings', [SettingsController::class, 'store']);
});
