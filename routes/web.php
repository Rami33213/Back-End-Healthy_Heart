<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\Admin\RecordsController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// ========== Admin Routes ==========
Route::prefix('admin')->name('admin.')->group(function () {
    
    // صفحات تسجيل الدخول (بدون middleware)
    Route::get('login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('login', [AuthController::class, 'login']);

    // الصفحات المحمية (تحتاج تسجيل دخول)
    Route::middleware('admin')->group(function () {
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::post('logout', [AuthController::class, 'logout'])->name('logout');

        // إدارة المستخدمين
        Route::get('users', [UsersController::class, 'index'])->name('users.index');
        Route::get('users/{user}', [UsersController::class, 'show'])->name('users.show');
        Route::delete('users/{user}', [UsersController::class, 'destroy'])->name('users.destroy');
        Route::post('users/{user}/toggle-status', [UsersController::class, 'toggleStatus'])->name('users.toggle-status');

        // السجلات الطبية
        Route::get('records/ecg', [RecordsController::class, 'ecg'])->name('records.ecg');
        Route::get('records/heart-rate', [RecordsController::class, 'heartRate'])->name('records.heart-rate');
        Route::get('records/medical-tests', [RecordsController::class, 'medicalTests'])->name('records.medical-tests');
        Route::get('records/consultations', [RecordsController::class, 'consultations'])->name('records.consultations');
        Route::get('records/diagnosis', [RecordsController::class, 'diagnosis'])->name('records.diagnosis');
    });
});
