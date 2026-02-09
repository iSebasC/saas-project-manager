<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\MeController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\HealthController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

// Health check routes (no auth required)
Route::get('/health', [HealthController::class, 'health'])->name('health');
Route::get('/test-db', [HealthController::class, 'testDatabase'])->name('test.db');
Route::get('/info', [HealthController::class, 'info'])->name('info');

// Public routes
Route::post('/auth/register', RegisterController::class)->name('auth.register');
Route::post('/auth/login', LoginController::class)->name('auth.login');

// Protected routes
Route::middleware(['auth:sanctum'])->group(function () {
    // Auth routes
    Route::post('/auth/logout', LogoutController::class)->name('auth.logout');
    Route::get('/auth/me', MeController::class)->name('auth.me');

    // Projects routes
    Route::apiResource('projects', ProjectController::class);

    // Tasks routes
    Route::get('/projects/{project}/tasks', [TaskController::class, 'index'])->name('tasks.index');
    Route::post('/projects/{project}/tasks', [TaskController::class, 'store'])->name('tasks.store');
    Route::get('/tasks/{task}', [TaskController::class, 'show'])->name('tasks.show');
    Route::put('/tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');
    Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');
});
