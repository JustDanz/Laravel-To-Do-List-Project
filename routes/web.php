<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskController;


// Authentication Routes (for guests only)
Route::middleware(['guest'])->group(function () {
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// Logout Route (must be outside guest middleware)
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Authenticated Routes
Route::middleware(['auth'])->group(function () {
    // Dashboard/Home
    Route::get('/', [TaskController::class, 'index'])->name('home');
    
    // Calendar View
    Route::get('/calendar', [TaskController::class, 'calendar'])->name('calendar');
    
    // Task Resource Routes
    Route::resource('tasks', TaskController::class)->except(['index']);
    
    // Additional Task Endpoints
    Route::post('/tasks/{task}/status', [TaskController::class, 'updateStatus'])->name('tasks.update-status');
    Route::post('/tasks/{task}/update-day', [TaskController::class, 'updateDay'])->name('tasks.update-day');
});