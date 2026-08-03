<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\ExportController;

// Public Attendance Routes
Route::get('/', [AttendanceController::class, 'index'])->name('absen.index');
Route::post('/absen-masuk', [AttendanceController::class, 'masuk'])->name('absen.masuk');
Route::post('/absen-pulang', [AttendanceController::class, 'pulang'])->name('absen.pulang');

// Admin Guest Routes
Route::middleware('guest')->group(function () {
    Route::get('/admin/login', [AuthController::class, 'showLogin'])->name('admin.login');
    Route::post('/admin/login', [AuthController::class, 'login'])->name('admin.login.post');
});

// Admin Auth Protected Routes
Route::middleware('auth')->group(function () {
    Route::post('/admin/logout', [AuthController::class, 'logout'])->name('admin.logout');
    
    // Dashboard & Manual Entry
    Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::post('/admin/attendance/update', [DashboardController::class, 'updateAttendance'])->name('admin.attendance.update');
    Route::post('/admin/settings/update', [DashboardController::class, 'updateSettings'])->name('admin.settings.update');
    
    // Employee CRUD
    Route::get('/admin/karyawan', [EmployeeController::class, 'index'])->name('admin.karyawan');
    Route::post('/admin/karyawan', [EmployeeController::class, 'store'])->name('admin.karyawan.store');
    Route::put('/admin/karyawan/{id}', [EmployeeController::class, 'update'])->name('admin.karyawan.update');
    
    // Exports
    Route::get('/admin/export/mingguan', [ExportController::class, 'exportMingguan'])->name('admin.export.mingguan');
    Route::get('/admin/export/rincian/{employee}', [ExportController::class, 'exportRincian'])->name('admin.export.rincian');
});
