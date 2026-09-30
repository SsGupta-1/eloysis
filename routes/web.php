<?php

use App\Http\Controllers\Admin\AcademicClassController;
use App\Http\Controllers\Admin\AcademicSectionController;
use App\Http\Controllers\Admin\AcademicSessionController;
use App\Http\Controllers\Admin\AdmissionEnquiryController;
use App\Http\Controllers\Admin\AnnouncementController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\ClassSectionController;
use App\Http\Controllers\Admin\ClassSubjectController;
use App\Http\Controllers\Admin\ClassTimetableController;
use App\Http\Controllers\Admin\CommonController;
use App\Http\Controllers\Admin\ContactMessageController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EventController as AdminEventController;
use App\Http\Controllers\Admin\ExamController;
use App\Http\Controllers\Admin\GalleryController;
use App\Http\Controllers\Admin\HomePageBuilderController;
use App\Http\Controllers\Admin\HomeSliderController;
use App\Http\Controllers\Admin\ImportantMessageController;
use App\Http\Controllers\Admin\LogController;
use App\Http\Controllers\Admin\NewsController as AdminNewsController;
use App\Http\Controllers\Admin\PeriodsController;
use App\Http\Controllers\Admin\QuestionController;
use App\Http\Controllers\Admin\QuestionPaperController;
use App\Http\Controllers\Admin\QuickLinkController;
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
use App\Http\Controllers\Admin\TestimonialController;
use App\Http\Controllers\Admin\WebsiteSettingController;
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

        // ************Website Management Routes*******************
        // Home Page Builder
        Route::get('homepage-builder', [HomePageBuilderController::class, 'index'])->name('homepage-builder.index');
        Route::post('homepage-builder/orders', [HomePageBuilderController::class, 'updateOrders'])->name('homepage-builder.orders');
        Route::match(['patch', 'post'], 'homepage-builder/sections/{section}/status', [HomePageBuilderController::class, 'toggleStatus'])->name('homepage-builder.status');
        Route::match(['patch', 'post'], 'homepage-builder/sections/{section}/layout', [HomePageBuilderController::class, 'updateLayout'])->name('homepage-builder.layout');
        Route::match(['put', 'post'], 'homepage-builder/sections/{section}', [HomePageBuilderController::class, 'updateSection'])->name('homepage-builder.update');
        Route::post('homepage-builder/custom-section', [HomePageBuilderController::class, 'storeCustomSection'])->name('homepage-builder.custom-section.store');
        Route::match(['delete', 'post'], 'homepage-builder/custom-section/{section}', [HomePageBuilderController::class, 'destroyCustomSection'])->name('homepage-builder.custom-section.destroy');
        Route::get('homepage-builder/preview', [HomePageBuilderController::class, 'preview'])->name('homepage-builder.preview');

        // Announcements
        Route::post('homepage-builder/announcements', [HomePageBuilderController::class, 'storeAnnouncement'])->name('homepage-builder.announcements.store');
        Route::match(['put', 'post'], 'homepage-builder/announcements/{announcement}', [HomePageBuilderController::class, 'updateAnnouncement'])->name('homepage-builder.announcements.update');
        Route::match(['delete', 'post'], 'homepage-builder/announcements/{announcement}', [HomePageBuilderController::class, 'destroyAnnouncement'])->name('homepage-builder.announcements.destroy');

        // Important Messages
        Route::post('homepage-builder/messages', [HomePageBuilderController::class, 'storeImportantMessage'])->name('homepage-builder.messages.store');
        Route::match(['put', 'post'], 'homepage-builder/messages/{message}', [HomePageBuilderController::class, 'updateImportantMessage'])->name('homepage-builder.messages.update');
        Route::match(['delete', 'post'], 'homepage-builder/messages/{message}', [HomePageBuilderController::class, 'destroyImportantMessage'])->name('homepage-builder.messages.destroy');

        // Testimonials
        Route::post('homepage-builder/testimonials', [HomePageBuilderController::class, 'storeTestimonial'])->name('homepage-builder.testimonials.store');
        Route::match(['put', 'post'], 'homepage-builder/testimonials/{testimonial}', [HomePageBuilderController::class, 'updateTestimonial'])->name('homepage-builder.testimonials.update');
        Route::match(['delete', 'post'], 'homepage-builder/testimonials/{testimonial}', [HomePageBuilderController::class, 'destroyTestimonial'])->name('homepage-builder.testimonials.destroy');

        // Quick Links
        Route::post('homepage-builder/quick-links', [HomePageBuilderController::class, 'storeQuickLink'])->name('homepage-builder.quick-links.store');
        Route::match(['put', 'post'], 'homepage-builder/quick-links/{quick_link}', [HomePageBuilderController::class, 'updateQuickLink'])->name('homepage-builder.quick-links.update');
        Route::match(['delete', 'post'], 'homepage-builder/quick-links/{quick_link}', [HomePageBuilderController::class, 'destroyQuickLink'])->name('homepage-builder.quick-links.destroy');

        // Home Sliders
        Route::get('home-slider/list', [HomeSliderController::class, 'list'])->name('home-slider.list');
        Route::patch('home-slider/{home_slider}/status', [HomeSliderController::class, 'changeStatus'])->name('home-slider.status');
        Route::resource('home-slider', HomeSliderController::class);

        // Important Messages
        Route::get('important-messages/list', [ImportantMessageController::class, 'list'])->name('important-messages.list');
        Route::patch('important-messages/{important_message}/status', [ImportantMessageController::class, 'changeStatus'])->name('important-messages.status');
        Route::resource('important-messages', ImportantMessageController::class);

        // Announcements
        Route::get('announcements/list', [AnnouncementController::class, 'list'])->name('announcements.list');
        Route::patch('announcements/{announcement}/status', [AnnouncementController::class, 'changeStatus'])->name('announcements.status');
        Route::resource('announcements', AnnouncementController::class);

        // Quick Links
        Route::get('quick-links/list', [QuickLinkController::class, 'list'])->name('quick-links.list');
        Route::patch('quick-links/{quick_link}/status', [QuickLinkController::class, 'changeStatus'])->name('quick-links.status');
        Route::resource('quick-links', QuickLinkController::class);

        // Testimonials
        Route::get('testimonials/list', [TestimonialController::class, 'list'])->name('testimonials.list');
        Route::patch('testimonials/{testimonial}/status', [TestimonialController::class, 'changeStatus'])->name('testimonials.status');
        Route::resource('testimonials', TestimonialController::class);

        // News
        Route::get('news/list', [AdminNewsController::class, 'list'])->name('news.list');
        Route::patch('news/{news}/status', [AdminNewsController::class, 'changeStatus'])->name('news.status');
        Route::resource('news', AdminNewsController::class);

        // Events
        Route::get('events/list', [AdminEventController::class, 'list'])->name('events.list');
        Route::patch('events/{event}/status', [AdminEventController::class, 'changeStatus'])->name('events.status');
        Route::resource('events', AdminEventController::class);

        // Gallery
        Route::get('gallery/list', [GalleryController::class, 'list'])->name('gallery.list');
        Route::patch('gallery/{gallery}/status', [GalleryController::class, 'changeStatus'])->name('gallery.status');
        Route::resource('gallery', GalleryController::class);

        // Contact Messages
        Route::get('contact-messages/list', [ContactMessageController::class, 'list'])->name('contact-messages.list');
        Route::patch('contact-messages/{contact_message}/status', [ContactMessageController::class, 'updateStatus'])->name('contact-messages.status');
        Route::resource('contact-messages', ContactMessageController::class)->only(['index', 'show', 'destroy']);

        // Website Settings
        Route::get('website-settings', [WebsiteSettingController::class, 'index'])->name('website-settings.index');
        Route::post('website-settings', [WebsiteSettingController::class, 'update'])->name('website-settings.update');

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

        // **********************Examination & Question Paper Management******************************* */
        Route::get('questions/list', [QuestionController::class, 'list'])->name('questions.list');
        Route::get('questions/search-selection', [QuestionController::class, 'searchForSelection'])->name('questions.search-selection');
        Route::patch('questions/{question}/status', [QuestionController::class, 'changeStatus'])->name('questions.status');
        Route::resource('questions', QuestionController::class);

        Route::get('question-papers/list', [QuestionPaperController::class, 'list'])->name('question-papers.list');
        Route::post('question-papers/auto-blueprint', [QuestionPaperController::class, 'autoGenerateBlueprint'])->name('question-papers.auto-blueprint');
        Route::patch('question-papers/{question_paper}/lock', [QuestionPaperController::class, 'toggleLock'])->name('question-papers.lock');
        Route::post('question-papers/{question_paper}/approval', [QuestionPaperController::class, 'handleApproval'])->name('question-papers.approval');
        Route::post('question-papers/{question_paper}/generate-sets', [QuestionPaperController::class, 'generateSets'])->name('question-papers.generate-sets');
        Route::get('question-papers/{question_paper}/print', [QuestionPaperController::class, 'print'])->name('question-papers.print');
        Route::resource('question-papers', QuestionPaperController::class);

        Route::get('exams/list', [ExamController::class, 'list'])->name('exams.list');
        Route::patch('exams/{exam}/status', [ExamController::class, 'changeStatus'])->name('exams.status');
        Route::get('exams/{exam}/enrollments', [ExamController::class, 'enrollments'])->name('exams.enrollments');
        Route::post('exams/{exam}/enrollment-eligibility', [ExamController::class, 'updateStudentEligibility'])->name('exams.enrollment-eligibility');
        Route::get('exams/{exam}/admit-cards', [ExamController::class, 'admitCards'])->name('exams.admit-cards');
        Route::get('exams/schedules/{schedule}/marks-entry', [ExamController::class, 'marksEntry'])->name('exams.schedules.marks-entry');
        Route::post('exams/schedules/{schedule}/save-marks', [ExamController::class, 'saveMarks'])->name('exams.schedules.save-marks');
        Route::resource('exams', ExamController::class);

        Route::get('results', [ResultController::class, 'index'])->name('results.index');
        Route::get('results/list', [ResultController::class, 'list'])->name('results.list');
        Route::get('results/exam/{exam}', [ResultController::class, 'examResults'])->name('results.exam');
        Route::post('results/exam/{exam}/publish', [ResultController::class, 'publishToggle'])->name('results.publish');
        Route::get('results/exam/{exam}/tabulation-print', [ResultController::class, 'tabulationPrint'])->name('results.tabulation-print');
        Route::get('results/student/{student}', [ResultController::class, 'studentResults'])->name('results.student');
    });
});
