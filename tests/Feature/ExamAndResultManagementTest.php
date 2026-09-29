<?php

use App\Models\AcademicClass;
use App\Models\AcademicSession;
use App\Models\Exam;
use App\Models\Question;
use App\Models\QuestionPaper;
use App\Models\Role;
use App\Models\Section;
use App\Models\StudentEnrollment;
use App\Models\StudentProfile;
use App\Models\Subject;
use App\Models\User;
use App\Services\Admin\ExamService;
use App\Services\Admin\QuestionPaperService;
use App\Services\Admin\QuestionService;
use App\Services\Admin\ResultService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

test('complete end-to-end workflow for questions, question papers, exams, and results', function () {
    // 1. Setup Super Admin, Role, Session, Class, Section, Subjects
    $adminRole = Role::firstOrCreate(
        ['slug' => 'super_admin'],
        ['role_name' => 'Super Admin', 'status' => 1]
    );

    $adminUser = User::firstOrCreate(
        ['email' => 'admin_full_workflow@test.com'],
        [
            'name' => 'Exam Admin Officer',
            'mobile' => '97777'.rand(10000, 99999),
            'password' => Hash::make('password'),
            'role_id' => $adminRole->id,
            'status' => 1,
        ]
    );

    $session = AcademicSession::firstOrCreate(
        ['name' => '2026-2027'],
        ['start_date' => '2026-04-01', 'end_date' => '2027-03-31', 'status' => 1, 'is_current' => 1]
    );

    $class = AcademicClass::create([
        'class_name' => 'Class 10th-A '.uniqid(),
        'class_code' => 'C10-'.uniqid(),
        'status' => 1,
        'sort_order' => 10,
    ]);

    $section = Section::create([
        'name' => 'A',
        'code' => 'SEC-A-'.uniqid(),
        'status' => 1,
    ]);

    $subjectMath = Subject::create([
        'subject_name' => 'Mathematics '.uniqid(),
        'subject_code' => 'MATH-'.uniqid(),
        'status' => 1,
    ]);

    $subjectScience = Subject::create([
        'subject_name' => 'Science '.uniqid(),
        'subject_code' => 'SCI-'.uniqid(),
        'status' => 1,
    ]);

    // Create 3 Test Students and Enroll them
    $students = [];
    for ($i = 1; $i <= 3; $i++) {
        $stUser = User::create([
            'name' => "Student {$i} Test",
            'email' => "student{$i}_".uniqid().'@test.com',
            'mobile' => '9999'.rand(100000, 999999),
            'password' => Hash::make('password'),
            'role_id' => $adminRole->id,
            'status' => 1,
        ]);

        $stProfile = StudentProfile::create([
            'user_id' => $stUser->id,
            'admission_no' => 'ADM-2026-'.rand(1000, 9999),
            'father_name' => 'Guardian '.chr(64 + $i),
        ]);

        $enrollment = StudentEnrollment::create([
            'user_id' => $stUser->id,
            'stu_profile_id' => $stProfile->id,
            'academic_session_id' => $session->id,
            'class_id' => $class->id,
            'section_id' => $section->id,
            'roll_number' => $i,
            'status' => 1,
        ]);

        $students[] = $enrollment;
    }

    Auth::guard('admin')->setUser($adminUser);

    // ==========================================
    // MODULE 1: QUESTION MANAGEMENT & PAPERS
    // ==========================================
    $qService = app(QuestionService::class);
    $q1 = $qService->create([
        'class_id' => $class->id,
        'subject_id' => $subjectMath->id,
        'academic_session_id' => $session->id,
        'question_type' => 'mcq',
        'difficulty_level' => 'medium',
        'question_text' => 'What is 15 x 12?',
        'options' => ['a' => '180', 'b' => '160', 'c' => '175', 'd' => '190'],
        'correct_option' => 'a',
        'marks' => 2.00,
        'status' => 1,
    ]);

    expect($q1)->toBeInstanceOf(Question::class);
    expect($q1->correct_option)->toBe('a');

    $qpService = app(QuestionPaperService::class);
    $paper = $qpService->create([
        'academic_session_id' => $session->id,
        'class_id' => $class->id,
        'subject_id' => $subjectMath->id,
        'title' => 'Maths Mid-Term 2026',
        'paper_code' => 'QP-MATH-'.uniqid(),
        'total_marks' => 2.00,
        'duration_minutes' => 60,
        'sections' => [
            [
                'section_name' => 'Section A',
                'section_type' => 'mcq',
                'marks_per_question' => 2.00,
                'questions' => [['id' => $q1->id, 'marks' => 2.00]],
            ],
        ],
    ]);

    expect($paper)->toBeInstanceOf(QuestionPaper::class);
    expect($paper->items)->toHaveCount(1);

    // Approve Paper
    $approvedPaper = $qpService->handleApproval($paper->id, QuestionPaper::APPROVAL_APPROVED, 'Verified by HOD');
    expect($approvedPaper->approval_status)->toBe(QuestionPaper::APPROVAL_APPROVED);

    // ==========================================
    // MODULE 2: EXAM MANAGEMENT
    // ==========================================
    $examService = app(ExamService::class);

    $exam = $examService->create([
        'academic_session_id' => $session->id,
        'class_id' => $class->id,
        'title' => 'Mid-Term Examination 2026',
        'exam_code' => 'EXM-MID-'.uniqid(),
        'exam_type' => 'mid_term',
        'exam_mode' => 'offline',
        'total_marks' => 200.00,
        'passing_marks' => 66.00,
        'duration_minutes' => 180,
        'start_date' => '2026-10-10',
        'end_date' => '2026-10-20',
        'status' => 'published',
        'schedules' => [
            [
                'subject_id' => $subjectMath->id,
                'question_paper_id' => $approvedPaper->id,
                'exam_date' => '2026-10-10',
                'start_time' => '09:00:00',
                'end_time' => '12:00:00',
                'duration_minutes' => 180,
                'room_no' => 'Hall 101',
                'max_theory_marks' => 80.00,
                'max_practical_marks' => 0.00,
                'max_internal_marks' => 20.00,
                'max_viva_marks' => 0.00,
                'total_marks' => 100.00,
                'passing_marks' => 33.00,
            ],
            [
                'subject_id' => $subjectScience->id,
                'exam_date' => '2026-10-12',
                'start_time' => '09:00:00',
                'end_time' => '12:00:00',
                'duration_minutes' => 180,
                'room_no' => 'Hall 102',
                'max_theory_marks' => 70.00,
                'max_practical_marks' => 30.00,
                'max_internal_marks' => 0.00,
                'max_viva_marks' => 0.00,
                'total_marks' => 100.00,
                'passing_marks' => 33.00,
            ],
        ],
    ]);

    expect($exam)->toBeInstanceOf(Exam::class);
    expect($exam->schedules)->toHaveCount(2);
    // Students enrolled automatically
    expect($exam->enrolledStudents)->toHaveCount(3);

    // Test Admit Card Generation
    $admitCardsData = $examService->getAdmitCardsData($exam->id);
    expect($admitCardsData['students'])->toHaveCount(3);
    expect($admitCardsData['schedules'])->toHaveCount(2);

    // Test Matrix Marks Entry for Math (Schedule 1)
    $mathSchedule = $exam->schedules->where('subject_id', $subjectMath->id)->first();
    $mathMarksCount = $examService->saveMarks($mathSchedule->id, [
        [
            'student_enrollment_id' => $students[0]->id,
            'theory_marks' => 75.00,
            'internal_marks' => 18.00,
            'is_absent' => false,
        ], // 93 / 100 (A+)
        [
            'student_enrollment_id' => $students[1]->id,
            'theory_marks' => 60.00,
            'internal_marks' => 15.00,
            'is_absent' => false,
        ], // 75 / 100 (B+)
        [
            'student_enrollment_id' => $students[2]->id,
            'theory_marks' => 20.00,
            'internal_marks' => 5.00,
            'is_absent' => false,
        ], // 25 / 100 (F)
    ]);
    expect($mathMarksCount)->toBe(3);

    // Test Matrix Marks Entry for Science (Schedule 2)
    $scienceSchedule = $exam->schedules->where('subject_id', $subjectScience->id)->first();
    $scienceMarksCount = $examService->saveMarks($scienceSchedule->id, [
        [
            'student_enrollment_id' => $students[0]->id,
            'theory_marks' => 65.00,
            'practical_marks' => 28.00,
            'is_absent' => false,
        ], // 93 / 100 (A+)
        [
            'student_enrollment_id' => $students[1]->id,
            'theory_marks' => 55.00,
            'practical_marks' => 25.00,
            'is_absent' => false,
        ], // 80 / 100 (A)
        [
            'student_enrollment_id' => $students[2]->id,
            'theory_marks' => 40.00,
            'practical_marks' => 20.00,
            'is_absent' => false,
        ], // 60 / 100 (B)
    ]);
    expect($scienceMarksCount)->toBe(3);

    // ==========================================
    // MODULE 3: RESULT MANAGEMENT & BROADSHEET
    // ==========================================
    $resultService = app(ResultService::class);

    // 1. Broadsheet Calculation
    $broadsheet = $resultService->getExamBroadsheet($exam->id);
    expect($broadsheet['schedules'])->toHaveCount(2);
    expect($broadsheet['students_data'])->toHaveCount(3);
    expect($broadsheet['summary']['total_students'])->toBe(3);
    expect($broadsheet['summary']['passed_students'])->toBe(2);
    expect($broadsheet['summary']['compartment_students'])->toBe(1); // Student 3 failed Math (25) but passed Science (60)
    expect($broadsheet['summary']['highest_marks'])->toBe(186.0); // Student 1: 93 + 93 = 186

    // Verify Student 1 Rank is #1
    $topStudent = $broadsheet['students_data']->firstWhere('student_enrollment_id', $students[0]->id);
    expect($topStudent['class_rank'])->toBe(1);
    expect($topStudent['percentage'])->toBe(93.0);
    expect($topStudent['result_status'])->toBe('PASS');

    // 2. Individual Report Card Generation
    $reportCard = $resultService->getStudentReportCard($exam->id, $students[0]->id);
    expect($reportCard['total_max'])->toBe(200.0);
    expect($reportCard['total_obtained'])->toBe(186.0);
    expect($reportCard['overall_grade'])->toBe('A+');
    expect($reportCard['result_status'])->toBe('PASSED');
    expect($reportCard['class_rank'])->toBe(1);
    expect($reportCard['subject_rows'])->toHaveCount(2);

    // 3. Toggle Publish
    $publishedExam = $resultService->togglePublish($exam->id, true);
    expect($publishedExam->is_published)->toBeTrue();

    // 4. Test HTTP Endpoints
    $actingAdmin = test()->actingAs($adminUser, 'admin');

    $examListRes = $actingAdmin->getJson(route('admin.exams.list'));
    $examListRes->assertStatus(200);

    $examShowRes = $actingAdmin->get(route('admin.exams.show', $exam->id));
    $examShowRes->assertStatus(200);

    $admitCardRes = $actingAdmin->get(route('admin.exams.admit-cards', $exam->id));
    $admitCardRes->assertStatus(200);

    $marksEntryRes = $actingAdmin->get(route('admin.exams.schedules.marks-entry', $mathSchedule->id));
    $marksEntryRes->assertStatus(200);

    $resultsIndexRes = $actingAdmin->get(route('admin.results.index'));
    $resultsIndexRes->assertStatus(200);

    $broadsheetRes = $actingAdmin->get(route('admin.results.exam', $exam->id));
    $broadsheetRes->assertStatus(200);

    $reportCardRes = $actingAdmin->get(route('admin.results.student', ['student' => $students[0]->id, 'exam_id' => $exam->id]));
    $reportCardRes->assertStatus(200);

    $tabulationPrintRes = $actingAdmin->get(route('admin.results.tabulation-print', $exam->id));
    $tabulationPrintRes->assertStatus(200);
});
