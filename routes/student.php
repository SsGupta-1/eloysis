<?php

use App\Http\Controllers\Student\AuthController as StudentAuthController;
use App\Http\Controllers\Student\DashboardController as StudentDashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Student Portal Routes
|--------------------------------------------------------------------------
*/

Route::prefix('student')->name('student.')->group(function () {

    // Guest student routes (Login)
    Route::middleware('student.guest')->group(function () {
        Route::get('/login', [StudentAuthController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [StudentAuthController::class, 'login'])->name('login.submit');
    });

    // Authenticated student routes (Portal)
    Route::middleware('student.auth')->group(function () {
        Route::post('/logout', [StudentAuthController::class, 'logout'])->name('logout');

        // Dashboard Hub
        Route::get('/dashboard', [StudentDashboardController::class, 'dashboard'])->name('dashboard');

        // Student Profile & Password Change
        Route::get('/profile', [StudentDashboardController::class, 'profile'])->name('profile');
        Route::post('/profile/password', [StudentDashboardController::class, 'updatePassword'])->name('profile.password');

        // Class Timetable
        Route::get('/timetable', [StudentDashboardController::class, 'timetable'])->name('timetable');

        // Attendance Log & History
        Route::get('/attendance', [StudentDashboardController::class, 'attendance'])->name('attendance');

        // Examinations & Schedules
        Route::get('/exams', [StudentDashboardController::class, 'exams'])->name('exams');
        Route::get('/exams/{exam}/admit-card', [StudentDashboardController::class, 'admitCard'])->name('exams.admit-card');

        // Results & Marksheets
        Route::get('/results', [StudentDashboardController::class, 'results'])->name('results');
        Route::get('/results/{exam}/marksheet', [StudentDashboardController::class, 'marksheet'])->name('results.marksheet');

        // Fees & Dues
        Route::get('/fees', [StudentDashboardController::class, 'fees'])->name('fees');
    });
});
