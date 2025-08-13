<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::middleware('check_login')->group(function(){
    Route::get('/', [DashboardController::class, 'showTemplate'])->name("dashboard");
});

Route::middleware('tackle_admin_exists')->group(function(){
    // Auth
    Route::get('/welcome', [AuthController::class, 'initialize'])->name("welcome");
    Route::get('/create-admin', [AuthController::class, 'showCreateAdmin'])->name('create.admin');
});

Route::middleware('admin_exists')->group(function(){
    // Auth
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::get('/lupa-password', [AuthController::class, 'showPassword'])->name('password');
    Route::get('/ganti-password', [AuthController::class, 'showGantiPassword'])->name('ganti-password');
    Route::get('/otp', [AuthController::class, 'showOtp'])->name('otp');
});
