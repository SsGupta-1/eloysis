<?php

use App\Http\Controllers\Api\AttemptController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\ExamController;
use App\Http\Controllers\Api\ResultController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    // Public
    Route::post('/login', [AuthController::class, 'login'])->name('login');

    // Protected student routes
    Route::middleware(['auth:sanctum'])->group(function () {

        Route::post('/logout', [AuthController::class, 'logout']);

        Route::get('/profile', [AuthController::class, 'getProfile']);
        Route::get('/dashboard', [DashboardController::class, 'index']);

        Route::get('/exams', [ExamController::class, 'index']);
        Route::get('/exams/{exam}', [ExamController::class, 'show']);

        Route::post('/exams/{exam}/start', [AttemptController::class, 'start']);
        Route::get('/attempts/{attempt}', [AttemptController::class, 'show']);
        Route::post('/attempts/{attempt}/save-answer', [AttemptController::class, 'saveAnswer']);
        Route::post('/attempts/{attempt}/submit', [AttemptController::class, 'submit']);

        Route::get('/results', [ResultController::class, 'index']);
        Route::get('/results/{attempt}', [ResultController::class, 'show']);
    });
});
