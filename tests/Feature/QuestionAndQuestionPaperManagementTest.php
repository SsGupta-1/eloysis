<?php

use App\Models\AcademicClass;
use App\Models\AcademicSession;
use App\Models\Question;
use App\Models\QuestionPaper;
use App\Models\Role;
use App\Models\Subject;
use App\Models\User;
use App\Services\Admin\QuestionPaperService;
use App\Services\Admin\QuestionService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

test('question bank and question paper management full workflow', function () {
    // 1. Setup Admin, Session, Class, Subject
    $adminRole = Role::firstOrCreate(
        ['slug' => 'super_admin'],
        ['role_name' => 'Super Admin', 'status' => 1]
    );

    $adminUser = User::firstOrCreate(
        ['email' => 'admin_qp_test@test.com'],
        [
            'name' => 'Admin QP Tester',
            'mobile' => '98888'.rand(10000, 99999),
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
        'class_name' => 'Class 10th Test '.uniqid(),
        'class_code' => 'C10-'.uniqid(),
        'status' => 1,
        'sort_order' => 10,
    ]);

    $subject = Subject::create([
        'subject_name' => 'Physics '.uniqid(),
        'subject_code' => 'PHY-'.uniqid(),
        'status' => 1,
    ]);

    Auth::guard('admin')->setUser($adminUser);

    // 2. Test Question Creation via QuestionService
    $questionService = app(QuestionService::class);
    $q1 = $questionService->create([
        'class_id' => $class->id,
        'subject_id' => $subject->id,
        'academic_session_id' => $session->id,
        'chapter_name' => 'Optics',
        'topic_name' => 'Refraction',
        'question_type' => 'mcq',
        'difficulty_level' => 'easy',
        'blooms_taxonomy' => 'remember',
        'question_text' => 'What is the speed of light in vacuum?',
        'options' => [
            'a' => '3 x 10^8 m/s',
            'b' => '2 x 10^8 m/s',
            'c' => '1.5 x 10^8 m/s',
            'd' => '3 x 10^6 m/s',
        ],
        'correct_option' => 'a',
        'marks' => 1.00,
        'negative_marks' => 0.25,
        'status' => 1,
    ]);

    expect($q1)->toBeInstanceOf(Question::class);
    expect($q1->option_a)->toBe('3 x 10^8 m/s');
    expect($q1->correct_option)->toBe('a');

    // Create 3 more questions for paper testing
    $q2 = $questionService->create([
        'class_id' => $class->id,
        'subject_id' => $subject->id,
        'question_type' => 'mcq',
        'difficulty_level' => 'medium',
        'question_text' => 'Which lens is used to correct myopia?',
        'options' => ['a' => 'Convex', 'b' => 'Concave', 'c' => 'Bifocal', 'd' => 'Cylindrical'],
        'correct_option' => 'b',
        'marks' => 1.00,
        'status' => 1,
    ]);

    $q3 = $questionService->create([
        'class_id' => $class->id,
        'subject_id' => $subject->id,
        'question_type' => 'short_answer',
        'difficulty_level' => 'medium',
        'question_text' => 'State Snell\'s Law of refraction.',
        'marks' => 3.00,
        'status' => 1,
    ]);

    // 3. Test Question Paper Creation with Sections and Items
    $paperService = app(QuestionPaperService::class);
    $paperData = [
        'academic_session_id' => $session->id,
        'class_id' => $class->id,
        'subject_id' => $subject->id,
        'title' => 'Physics Mid-Term Exam 2026',
        'paper_code' => 'QP-TEST-'.uniqid(),
        'total_marks' => 5.00,
        'duration_minutes' => 90,
        'instructions' => 'Answer all questions.',
        'is_confidential' => true,
        'sections' => [
            [
                'section_name' => 'Section A - Objective',
                'section_type' => 'mcq',
                'marks_per_question' => 1.00,
                'questions' => [
                    ['id' => $q1->id, 'marks' => 1.00],
                    ['id' => $q2->id, 'marks' => 1.00],
                ],
            ],
            [
                'section_name' => 'Section B - Short Answer',
                'section_type' => 'short_answer',
                'marks_per_question' => 3.00,
                'questions' => [
                    ['id' => $q3->id, 'marks' => 3.00],
                ],
            ],
        ],
    ];

    $paper = $paperService->create($paperData);
    expect($paper)->toBeInstanceOf(QuestionPaper::class);
    expect($paper->sections)->toHaveCount(2);
    expect($paper->items)->toHaveCount(3);
    expect($paper->is_confidential)->toBeTrue();
    expect($paper->is_locked)->toBeFalse();

    // 4. Test Multi-Set Generation (Sets A, B, C, D)
    $paperWithSets = $paperService->generateSets($paper->id, ['A', 'B', 'C', 'D'], true);
    expect($paperWithSets->has_sets)->toBeTrue();
    expect($paperWithSets->set_names)->toBe(['A', 'B', 'C', 'D']);
    // 3 questions * 4 sets = 12 items
    expect($paperWithSets->items->where('set_code', 'A'))->toHaveCount(3);
    expect($paperWithSets->items->where('set_code', 'B'))->toHaveCount(3);

    // 5. Test Paper Lock / Unlock
    $lockedPaper = $paperService->toggleLock($paper->id);
    expect($lockedPaper->is_locked)->toBeTrue();

    // Verify modifying a locked paper throws an exception
    expect(fn () => $paperService->update($paper->id, ['title' => 'Hacked Title']))
        ->toThrow(Exception::class, 'Question paper is locked and cannot be modified.');

    // Unlock
    $unlockedPaper = $paperService->toggleLock($paper->id);
    expect($unlockedPaper->is_locked)->toBeFalse();

    // 6. Test Approval Workflow
    $approvedPaper = $paperService->handleApproval($paper->id, QuestionPaper::APPROVAL_APPROVED, 'Approved by Head of Department');
    expect($approvedPaper->approval_status)->toBe(QuestionPaper::APPROVAL_APPROVED);
    expect($approvedPaper->approved_by)->toBe($adminUser->id);
    expect($approvedPaper->approved_at)->not->toBeNull();

    // 7. Test Audit Logs
    expect($approvedPaper->audits)->not->toBeEmpty();
    expect($approvedPaper->audits->where('action', 'approval_approved')->first())->not->toBeNull();

    // 8. Test HTTP Controller Endpoints
    $response = test()->actingAs($adminUser, 'admin')->get(route('admin.question-papers.print', $paper->id));
    $response->assertStatus(200);

    $listResponse = test()->actingAs($adminUser, 'admin')->getJson(route('admin.question-papers.list'));
    $listResponse->assertStatus(200);
});
