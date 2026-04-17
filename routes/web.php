<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TrackingController;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.process');
});

Route::middleware('auth')->group(function () {
    // Route ke halaman dashboard
    Route::get('/dashboard', function () {
        return view('dashboard'); 
    })->name('dashboard');
    
    // Route untuk menerima kordinat GPS dari JavaScript
    Route::post('/track-location', [TrackingController::class, 'updateLocation'])->name('track.location');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});