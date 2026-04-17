<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TrackingController;
use App\Http\Controllers\VehicleController;

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

    Route::get('/vehicles', [VehicleController::class, 'index'])->name('vehicles.index');
    Route::post('/vehicles', [VehicleController::class, 'store'])->name('vehicles.store');
    Route::post('/vehicles/{id}/active', [VehicleController::class, 'setActive'])->name('vehicles.active');

    Route::get('/history', [VehicleController::class, 'history'])->name('vehicles.history');

    Route::post('/vehicles/{id}/service', [VehicleController::class, 'serviceNow'])->name('vehicles.service');

    Route::post('/vehicles/deactivate', [VehicleController::class, 'deactivate'])->name('vehicles.deactivate');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});