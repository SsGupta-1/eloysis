<?php

use App\Http\Controllers\Admin\AcademicClassController;
use App\Http\Controllers\Admin\AcademicSectionController;
use App\Http\Controllers\Admin\AcademicSessionController;
use App\Http\Controllers\Admin\AdmissionEnquiryController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\ClassSectionController;
use App\Http\Controllers\Admin\ClassSubjectController;
use App\Http\Controllers\Admin\ClassTimetableController;
use App\Http\Controllers\Admin\CommonController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ExamController;
use App\Http\Controllers\Admin\ExamQuestionController;
use App\Http\Controllers\Admin\LogController;
use App\Http\Controllers\Admin\PeriodsController;
use App\Http\Controllers\Admin\QuestionController;
use App\Http\Controllers\Admin\ResultController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\Admin\StudentAttendanceController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\StudentPromotionController;
use App\Http\Controllers\Admin\SubjectController;
use App\Http\Controllers\Admin\TeacherAttendanceController;
use App\Http\Controllers\Admin\TeacherController;
use App\Http\Controllers\Admin\TeacherSubjectController;
use Illuminate\Support\Facades\Route;

require __DIR__.'/website.php';
// require __DIR__ . '/admin.php';

// Route::get('/', function () {
//     return view('welcome');
// });

Route::prefix('admin')->name('admin.')->middleware('activity.log')->group(function () {

    // Guest admin routes
    Route::middleware('admin.guest')->group(function () {
        Route::get('/login', [AuthController::class, 'index'])->name('login');
        Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
    });

    // ************Some common routes *********** */
    Route::get('sections/by-class/{classId}', [CommonController::class, 'byClass'])->name('sections.byClass');

    // Auth admin routes
    Route::middleware(['admin.auth'])->group(function () {
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // ************Roles route*******************
        Route::prefix('roles')
            ->name('roles.')
            ->controller(RoleController::class)
            ->group(function () {

                Route::get('/', 'index')->name('index');

                Route::get('/list', 'list')->name('list');

                Route::post('/', 'store')->name('store');

                Route::get('/{role}/edit', 'edit')->name('edit');

                Route::put('/{role}', 'update')->name('update');

                Route::delete('/{role}', 'destroy')->name('destroy');

                Route::patch('/{role}/status', 'changeStatus')->name('status');

            });
        // ************Academic Session route*******************
        Route::prefix('academic')
            ->name('academic.')
            ->controller(AcademicSessionController::class)
            ->group(function () {

                Route::get('/', 'index')->name('index');

                Route::get('/list', 'list')->name('list');

                Route::post('/', 'store')->name('store');

                Route::get('/{academic}/edit', 'edit')->name('edit');

                Route::put('/{academic}', 'update')->name('update');

                Route::delete('/{academic}', 'destroy')->name('destroy');

                Route::patch('/{academic}/status', 'changeStatus')->name('status');

            });

        // ************Academic Classes route*******************
        Route::get('classes/list', [AcademicClassController::class, 'list'])->name('classes.list');
        Route::patch('classes/{classes}/status', [AcademicClassController::class, 'changeStatus'])->name('classes.status');
        Route::resource('classes', AcademicClassController::class);

        // ************Academic Classes Section route*******************
        Route::get('sections/list', [AcademicSectionController::class, 'list'])->name('sections.list');
        Route::patch('sections/{sections}/status', [AcademicSectionController::class, 'changeStatus'])->name('sections.status');
        Route::resource('sections', AcademicSectionController::class);

        // ************Academic Student route*******************
        Route::get('students/list', [StudentController::class, 'list'])->name('students.list');
        Route::get('students/suggested-roll-number', [StudentController::class, 'getSuggestedRollNumber'])->name('students.suggested-roll-number');
        Route::patch('students/{students}/status', [StudentController::class, 'changeStatus'])->name('students.status');
        Route::resource('students', StudentController::class);

        // ************Academic Classes Subject route*******************
        Route::get('subjects/list', [SubjectController::class, 'list'])->name('subjects.list');
        Route::patch('subjects/{subjects}/status', [SubjectController::class, 'changeStatus'])->name('subjects.status');
        Route::resource('subjects', SubjectController::class);

        // ************Academic Teacher route*******************
        Route::get('teachers/list', [TeacherController::class, 'list'])->name('teachers.list');
        Route::patch('teachers/{teachers}/status', [TeacherController::class, 'changeStatus'])->name('teachers.status');
        Route::resource('teachers', TeacherController::class);

        // ************Academic Staff route*******************
        Route::get('staffs/list', [StaffController::class, 'list'])->name('staffs.list');
        Route::patch('staffs/{staffs}/status', [StaffController::class, 'changeStatus'])->name('staffs.status');
        Route::resource('staffs', StaffController::class);

        // ************Academic class subjects route*******************
        Route::get('clsubject/list', [ClassSubjectController::class, 'list'])->name('clsubject.list');
        Route::patch('clsubject/{clsubject}/status', [ClassSubjectController::class, 'changeStatus'])->name('clsubject.status');
        Route::resource('clsubject', ClassSubjectController::class);

        // ************Academic teacher subjects route*******************
        Route::get('teacher-subject/list', [TeacherSubjectController::class, 'list'])->name('teacher-subject.list');
        Route::patch('teacher-subject/{teacher_subject}/status', [TeacherSubjectController::class, 'changeStatus'])->name('teacher-subject.status');
        Route::resource('teacher-subject', TeacherSubjectController::class);

        // ************Academic Class Section route*******************
        Route::get('class-sections/list', [ClassSectionController::class, 'list'])->name('class-sections.list');
        Route::patch('class-sections/{classSection}/status', [ClassSectionController::class, 'changeStatus'])->name('class-sections.status');
        Route::resource('class-sections', ClassSectionController::class);

        // *****************Student Promotions route****************************** */
        Route::prefix('student-promotions')
            ->name('student-promotions.')
            ->controller(StudentPromotionController::class)
            ->group(function () {

                Route::get('/', 'index')->name('index');
                Route::get('/students', 'students')->name('students');
                Route::post('/promote', 'promote')->name('promote');

            });

        // **********************Student Attendance******************************* */

        Route::prefix('attendance')
            ->name('attendance.')
            ->controller(StudentAttendanceController::class)
            ->group(function () {

                Route::get('/', 'index')->name('index');
                Route::get('/students', 'students')->name('students');
                Route::post('/save', 'save')->name('save');
                Route::get('/history', 'history')->name('history');

            });

        // **********************Teacher Attendance******************************* */

        Route::prefix('teacher-attendance')
            ->name('teacher_attendance.')
            ->controller(TeacherAttendanceController::class)
            ->group(function () {

                Route::get('/', 'index')->name('index');
                Route::get('/list', 'list')->name('list');
                Route::post('/save', 'save')->name('save');
                Route::get('/history', 'history')->name('history');

            });

        // ************Academic Periods route*******************
        Route::get('periods/list', [PeriodsController::class, 'list'])->name('periods.list');
        Route::patch('periods/{periods}/status', [PeriodsController::class, 'changeStatus'])->name('periods.status');
        Route::resource('periods', PeriodsController::class);

        // ************Class Timetable*******************
        Route::get('class-timetables/list', [ClassTimetableController::class, 'list'])->name('class-timetables.list');
        Route::patch('class-timetables/{class_timetable}/status', [ClassTimetableController::class, 'changeStatus'])->name('class-timetables.status');
        Route::resource('class-timetables', ClassTimetableController::class);

        // ***************************Admission Enquiry******************************* */
        Route::get('admission-enquiry/list', [AdmissionEnquiryController::class, 'list'])->name('admission_enquiry.list');
        Route::patch('admission-enquiry/{admission_enquiry}/status', [AdmissionEnquiryController::class, 'changeStatus'])->name('admission_enquiry.status');
        Route::post('admission-enquiry/{admission_enquiry}/assign', [AdmissionEnquiryController::class, 'assignStaff'])->name('admission-enquiry.assign');
        Route::post('admission-enquiry/{admission_enquiry}/followup', [AdmissionEnquiryController::class, 'addFollowup'])->name('admission-enquiry.followup');
        Route::get('admission-enquiry/{admission_enquiry}/check-duplicates', [AdmissionEnquiryController::class, 'checkDuplicates'])->name('admission-enquiry.check-duplicates');
        Route::get('admission-enquiry/{admission_enquiry}/convert', [AdmissionEnquiryController::class, 'convert'])->name('admission-enquiry.convert');
        Route::post('admission-enquiry/{admission_enquiry}/convert', [AdmissionEnquiryController::class, 'storeConversion'])->name('admission-enquiry.convert-store');
        Route::put('admission-enquiry/{admission_enquiry}', [AdmissionEnquiryController::class, 'update'])->name('admission.enquiries.update');
        Route::resource('admission-enquiry', AdmissionEnquiryController::class);

        // **********************Log Management******************************* */
        Route::prefix('logs')
            ->name('logs.')
            ->controller(LogController::class)
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/files', 'files')->name('files');
                Route::get('/read', 'read')->name('read');
                Route::get('/download', 'download')->name('download');
            });

        Route::resource('exams', ExamController::class);
        Route::resource('questions', QuestionController::class);

        // Route::get('exams/{exam}/questions', [ExamQuestionController::class, 'index'])->name('exams.questions.index');
        // Route::post('exams/{exam}/questions', [ExamQuestionController::class, 'store'])->name('exams.questions.store');
        // Route::delete('exams/{exam}/questions/{question}', [ExamQuestionController::class, 'destroy'])->name('exams.questions.destroy');

        Route::get('results', [ResultController::class, 'index'])->name('results.index');
        Route::get('results/exam/{exam}', [ResultController::class, 'examResults'])->name('results.exam');
        Route::get('results/student/{student}', [ResultController::class, 'studentResults'])->name('results.student');
    });
});
