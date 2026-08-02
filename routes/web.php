<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\FloorController;
use App\Http\Controllers\MaintenanceLogController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Auth routes (no middleware)
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected routes
Route::middleware('auth')->group(function () {

    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/report', [DashboardController::class, 'report'])->name('report');

    // Floor
    Route::get('/floor', [FloorController::class, 'index'])->name('floor.index');
    Route::get('/floor/{room}', [FloorController::class, 'room'])->name('floor.room');

    // Devices — view (both admin and staff)
    Route::get('/devices', [DeviceController::class, 'index'])->name('devices.index');

    // Devices — admin only
    Route::middleware('admin')->group(function () {
        Route::get('/devices/create', [DeviceController::class, 'create'])->name('devices.create');
        Route::post('/devices', [DeviceController::class, 'store'])->name('devices.store');
        Route::get('/devices/{device}/edit', [DeviceController::class, 'edit'])->name('devices.edit');
        Route::put('/devices/{device}', [DeviceController::class, 'update'])->name('devices.update');
        Route::delete('/devices/{device}', [DeviceController::class, 'destroy'])->name('devices.destroy');
        Route::post('/devices/{device}/parts', [DeviceController::class, 'storePart'])->name('devices.parts.store');
        Route::put('/devices/{device}/parts/{part}', [DeviceController::class, 'updatePart'])->name('devices.parts.update');
        Route::delete('/devices/{device}/parts/{part}', [DeviceController::class, 'destroyPart'])->name('devices.parts.destroy');
        Route::patch('/devices/{device}/position', [DeviceController::class, 'updatePosition'])->name('devices.updatePosition');
        Route::put('/devices/{device}/quick-update', [DeviceController::class, 'quickUpdate'])->name('devices.quickUpdate');
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    });

    // Maintenance
    Route::get('/maintenance', [MaintenanceLogController::class, 'index'])->name('maintenance.index');
    Route::get('/maintenance/create', [MaintenanceLogController::class, 'create'])->name('maintenance.create');
    Route::get('/maintenance/create/{device}', [MaintenanceLogController::class, 'createForDevice'])->name('maintenance.create.device');
    Route::post('/maintenance', [MaintenanceLogController::class, 'store'])->name('maintenance.store');
    Route::get('/maintenance/{log}', [MaintenanceLogController::class, 'show'])->name('maintenance.show');
    Route::get('/maintenance/{log}/edit', [MaintenanceLogController::class, 'edit'])->name('maintenance.edit');
    Route::put('/maintenance/{log}', [MaintenanceLogController::class, 'update'])->name('maintenance.update');

    // Floor
    Route::middleware(['auth', 'admin'])->group(function () {
        Route::get('/rooms/create', [RoomController::class, 'create'])->name('rooms.create');
        Route::post('/rooms', [RoomController::class, 'store'])->name('rooms.store');
        Route::put('/rooms/{room}', [RoomController::class, 'update'])->name('rooms.update');
        Route::delete('/rooms/{room}', [RoomController::class, 'destroy'])->name('rooms.destroy');
    });
});
