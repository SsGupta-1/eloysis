<?php

use App\Http\Controllers\Api\AttemptController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\ExamController;
use App\Http\Controllers\Api\ResultController;
use App\Http\Controllers\Api\Student\StudentAttendanceController;
use App\Http\Controllers\Api\Student\StudentDashboardController;
use App\Http\Controllers\Api\Student\StudentExamController;
use App\Http\Controllers\Api\Student\StudentFeeController;
use App\Http\Controllers\Api\Student\StudentNotificationController;
use App\Http\Controllers\Api\Student\StudentProfileController;
use App\Http\Controllers\Api\Student\StudentResultController;
use App\Http\Controllers\Api\Student\StudentTimetableController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    // Public Authentication
    Route::post('/login', [AuthController::class, 'login'])->name('login');

    // Protected student routes
    Route::middleware(['auth:sanctum'])->group(function () {

        Route::post('/logout', [AuthController::class, 'logout'])->name('api.logout');

        /*
        |--------------------------------------------------------------------------
        | Student Mobile App Endpoints
        |--------------------------------------------------------------------------
        */
        Route::prefix('student')->name('api.student.')->group(function () {

            // 1. Dashboard (Flash alerts, timetable, attendance, exams, fees summary)
            Route::get('/dashboard', [StudentDashboardController::class, 'index'])->name('dashboard');

            // 2. Profile & Password
            Route::get('/profile', [StudentProfileController::class, 'show'])->name('profile');
            Route::post('/change-password', [StudentProfileController::class, 'changePassword'])->name('change-password');

            // 3. Class Timetable
            Route::get('/timetable', [StudentTimetableController::class, 'index'])->name('timetable');

            // 4. Attendance Stats & Monthly Logs
            Route::get('/attendance', [StudentAttendanceController::class, 'index'])->name('attendance');

            // 5. Examinations & Admit Cards
            Route::get('/exams', [StudentExamController::class, 'index'])->name('exams');
            Route::get('/exams/{id}', [StudentExamController::class, 'show'])->name('exams.show');
            Route::get('/exams/{id}/admit-card', [StudentExamController::class, 'admitCard'])->name('exams.admit-card');

            // 6. Results & Marksheet
            Route::get('/results', [StudentResultController::class, 'index'])->name('results');
            Route::get('/results/{examId}/marksheet', [StudentResultController::class, 'marksheet'])->name('results.marksheet');

            // 7. Fees, Dues & Receipts
            Route::get('/fees', [StudentFeeController::class, 'index'])->name('fees');
            Route::get('/fees/receipt/{paymentId}', [StudentFeeController::class, 'receipt'])->name('fees.receipt');

            // 8. Notifications & Flash Alerts
            Route::get('/notifications', [StudentNotificationController::class, 'index'])->name('notifications');
        });

        /*
        |--------------------------------------------------------------------------
        | Online CBT Test Attempts (Existing Legacy Endpoints)
        |--------------------------------------------------------------------------
        */
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
