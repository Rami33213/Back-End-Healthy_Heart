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


Route::get('/user', function (Request $request) {
  return $request->user();
})->middleware('auth:sanctum');
Route::post('register', [UserController::class, 'register']);
Route::post('login', [UserController::class, 'login']);
Route::post('logout', [UserController::class, 'logout'])->middleware('auth:sanctum');


Route::post('/profile', [ProfileController::class, 'storeOrUpdate']); //Update Profile Info
Route::get('/profile', [ProfileController::class, 'index']); //Get Profile Info


// ECGRecord
Route::post('/ECGRecord', [ECGRecordController::class, 'store']);

// كل السجل (History)
Route::get('/ECGRecord', [ECGRecordController::class, 'index']);

// آخر قياس فقط
Route::get('/ECGRecord/latest', [ECGRecordController::class, 'show']);



Route::post('/HeartRateRecord', [HeartRateRecordController::class, 'store']);
Route::get('/HeartRateRecord', [HeartRateRecordController::class, 'show']);


Route::post('/Diagnosis', [DiagnosisController::class, 'store']);
Route::get('/Diagnosis', [DiagnosisController::class, 'show']);


// الاستشارة الخبيرة
Route::post('/ExpertConsultation', [ExpertConsultationController::class, 'store']);
Route::get('/ExpertConsultation', [ExpertConsultationController::class, 'index']);   // كل السجل
Route::get('/ExpertConsultation/latest', [ExpertConsultationController::class, 'show']); // آخر واحدة فقط 


Route::get('/HeartRateRecord', [HeartRateRecordController::class, 'index']);

// إضافة قياس جديد (camera or upload)
Route::post('/HeartRateRecord', [HeartRateRecordController::class, 'store']);

// آخر قياس
Route::get('/HeartRateRecord/latest', [HeartRateRecordController::class, 'show']);

Route::post('/MedicalTest', [MedicalTestController::class, 'store']);
    Route::get('/MedicalTest/latest', [MedicalTestController::class, 'show']);

// حذف قياس
Route::delete('/HeartRateRecord/{id}', [HeartRateRecordController::class, 'destroy']);



Route::post('/Report', [ReportController::class, 'store']);
Route::get('/Report', [ReportController::class, 'show']);


Route::get('/settings', [SettingsController::class, 'show']);
Route::post('/settings', [SettingsController::class, 'store']);



