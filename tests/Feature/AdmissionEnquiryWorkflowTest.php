<?php

use App\Models\AcademicClass;
use App\Models\AcademicSession;
use App\Models\AdmissionEnquiry;
use App\Models\AdmissionEnquiryFollowup;
use App\Models\Role;
use App\Models\Section;
use App\Models\StudentEnrollment;
use App\Models\StudentProfile;
use App\Models\User;
use App\Services\Admin\AdmissionEnquiryService;
use App\Services\Admin\StudentService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

test('admission enquiry full crm and conversion workflow', function () {
    // 1. Ensure prerequisite records exist
    $adminRole = Role::firstOrCreate(
        ['slug' => 'super_admin'],
        ['role_name' => 'Super Admin', 'status' => 1]
    );

    $studentRole = Role::firstOrCreate(
        ['slug' => 'student'],
        ['role_name' => 'Student', 'status' => 1]
    );

    $adminUser = User::firstOrCreate(
        ['email' => 'admin_test@test.com'],
        [
            'name' => 'Test Admin',
            'mobile' => '9999999991',
            'password' => Hash::make('password'),
            'role_id' => $adminRole->id,
            'status' => 1,
        ]
    );

    $staffUser = User::firstOrCreate(
        ['email' => 'staff_test@test.com'],
        [
            'name' => 'Test Staff Counsellor',
            'mobile' => '9999999992',
            'password' => Hash::make('password'),
            'role_id' => $adminRole->id,
            'status' => 1,
        ]
    );

    $session = AcademicSession::firstOrCreate(
        ['name' => '2026-2027'],
        ['start_date' => '2026-04-01', 'end_date' => '2027-03-31', 'status' => 1, 'is_current' => 1]
    );

    $class = AcademicClass::firstOrCreate(
        ['class_name' => 'Class 10th Test'],
        ['status' => 1, 'sort_order' => 10]
    );

    $section = Section::firstOrCreate(
        ['name' => 'Section A Test'],
        ['status' => 1]
    );

    Auth::guard('admin')->setUser($adminUser);

    // 2. Create Admission Enquiry
    $service = app(AdmissionEnquiryService::class);
    $enquiry = $service->create([
        'academic_session_id' => $session->id,
        'application_no' => 'ENQ-TEST-'.uniqid(),
        'student_name' => 'Rahul Sharma',
        'student_email' => 'rahul_'.uniqid().'@example.com',
        'student_phone' => '98765'.rand(10000, 99999),
        'date_of_birth' => '2010-05-15',
        'gender' => 'male',
        'parent_name' => 'Manoj Sharma',
        'parent_phone' => '91234'.rand(10000, 99999),
        'class_id' => $class->id,
        'source' => 'website',
        'status' => 'new',
        'message' => 'Looking for admission in class 10th.',
    ]);

    expect($enquiry)->toBeInstanceOf(AdmissionEnquiry::class);
    expect($enquiry->status)->toBe('new');

    // 3. Test Staff Assignment
    $assignedEnquiry = $service->assignStaff($enquiry, $staffUser->id, 'Assigned to senior counsellor.');
    expect($assignedEnquiry->assigned_to)->toBe($staffUser->id);
    expect($assignedEnquiry->status)->toBe('assigned');

    // Verify activity logged
    $assignmentActivity = AdmissionEnquiryFollowup::where('admission_enquiry_id', $enquiry->id)
        ->where('action_type', 'assignment')
        ->first();
    expect($assignmentActivity)->not->toBeNull();

    // 4. Test Adding Follow-up
    $followup = $service->addFollowup($enquiry, [
        'attempt_status' => 'connected',
        'status' => 'interested',
        'remarks' => 'Parent visited campus and confirmed interested.',
        'next_follow_up_at' => now()->addDays(2),
    ]);

    expect($followup)->toBeInstanceOf(AdmissionEnquiryFollowup::class);
    $enquiry->refresh();
    expect($enquiry->status)->toBe('interested');
    expect($enquiry->attempt_count)->toBe(1);

    // 5. Test Duplicate Detection
    $duplicates = $service->checkDuplicates($enquiry);
    expect($duplicates)->toBeArray();

    // 6. Test Convert to Admission (Atomically creates User, StudentProfile & StudentEnrollment)
    $admissionNo = 'ADM-TEST-'.uniqid();
    $studentData = [
        'name' => $enquiry->student_name,
        'email' => $enquiry->student_email,
        'mobile' => $enquiry->student_phone,
        'password' => 'secret1234',
        'status' => 1,
        'admission_no' => $admissionNo,
        'roll_number' => '101',
        'academic_session_id' => $session->id,
        'class_id' => $class->id,
        'section_id' => $section->id,
        'admission_date' => now()->format('Y-m-d'),
        'dob' => $enquiry->date_of_birth->format('Y-m-d'),
        'gender' => $enquiry->gender,
        'father_name' => $enquiry->parent_name,
        'guardian_name' => $enquiry->parent_name,
        'guardian_mobile' => $enquiry->parent_phone,
    ];

    $conversionResult = $service->convertToAdmission($enquiry, $studentData);

    expect($conversionResult)->toHaveKeys(['student', 'enrollment', 'enquiry']);
    expect($conversionResult['student'])->toBeInstanceOf(StudentProfile::class);
    expect($conversionResult['enrollment'])->toBeInstanceOf(StudentEnrollment::class);

    // Verify Enquiry is marked Converted and linked
    $enquiry->refresh();
    expect($enquiry->isConverted())->toBeTrue();
    expect($enquiry->status)->toBe('converted');
    expect($enquiry->student_profile_id)->toBe($conversionResult['student']->id);
    expect($enquiry->enrollment_id)->toBe($conversionResult['enrollment']->id);
    expect($enquiry->converted_user_id)->toBe($conversionResult['student']->user_id);

    // 7. Verify Double Conversion Prevention
    expect(fn () => $service->convertToAdmission($enquiry, $studentData))
        ->toThrow(Exception::class, 'This enquiry has already been converted to an admission.');
});

test('admin can view create form and manually create admission enquiry', function () {
    $adminRole = Role::firstOrCreate(
        ['slug' => 'super_admin'],
        ['role_name' => 'Super Admin', 'status' => 1]
    );

    $adminUser = User::updateOrCreate(
        ['email' => 'admin_creator@test.com'],
        [
            'name' => 'Admin Creator',
            'mobile' => '9999999993',
            'password' => Hash::make('password'),
            'role_id' => $adminRole->id,
            'status' => 1,
        ]
    );
    $adminUser->load('role');

    $session = AcademicSession::firstOrCreate(
        ['name' => '2026-2027'],
        ['start_date' => '2026-04-01', 'end_date' => '2027-03-31', 'status' => 1, 'is_current' => 1]
    );

    $class = AcademicClass::firstOrCreate(
        ['class_name' => 'Class 9th Test'],
        ['status' => 1, 'sort_order' => 9]
    );

    Auth::guard('admin')->setUser($adminUser);

    $response = $this->actingAs($adminUser, 'admin')->get(route('admin.admission-enquiry.create'));
    $response->assertStatus(200);

    $postData = [
        'student_name' => 'Amit Verma',
        'student_email' => 'amit_'.uniqid().'@example.com',
        'student_phone' => '98111'.rand(10000, 99999),
        'parent_name' => 'Suresh Verma',
        'parent_phone' => '98222'.rand(10000, 99999),
        'academic_session_id' => $session->id,
        'class_id' => $class->id,
        'source' => 'walk_in',
        'gender' => 'male',
        'message' => 'Walk-in enquiry recorded by admin.',
        'remarks' => 'Visited the front desk.',
    ];

    $storeResponse = $this->actingAs($adminUser, 'admin')->postJson(route('admin.admission-enquiry.store'), $postData);
    $storeResponse->assertStatus(200)
        ->assertJson([
            'status' => true,
            'message' => 'Admission Enquiry created successfully.',
        ]);

    $createdEnquiry = AdmissionEnquiry::where('student_email', $postData['student_email'])->first();
    expect($createdEnquiry)->not->toBeNull();
    expect($createdEnquiry->source)->toBe('walk_in');
    expect($createdEnquiry->application_no)->not->toBeEmpty();
});

test('suggested roll number is calculated uniquely per session class and section and validated against duplicates', function () {
    $adminRole = Role::firstOrCreate(
        ['slug' => 'super_admin'],
        ['role_name' => 'Super Admin', 'status' => 1]
    );

    $adminUser = User::firstOrCreate(
        ['email' => 'admin_roll_test@test.com'],
        [
            'name' => 'Admin Roll Test',
            'mobile' => '9999999994',
            'password' => Hash::make('password'),
            'role_id' => $adminRole->id,
            'status' => 1,
        ]
    );

    $session = AcademicSession::firstOrCreate(
        ['name' => '2026-2027'],
        ['start_date' => '2026-04-01', 'end_date' => '2027-03-31', 'status' => 1, 'is_current' => 1]
    );

    $class = AcademicClass::create(
        ['class_name' => 'Class Unique Roll '.uniqid(), 'status' => 1, 'sort_order' => 12]
    );

    $section = Section::create(
        ['name' => 'Section Unique Roll '.uniqid(), 'status' => 1]
    );

    Auth::guard('admin')->setUser($adminUser);

    $studentService = app(StudentService::class);

    // Initial roll number should be '1' when none exist
    $initialRoll = $studentService->generateSuggestedRollNumber($session->id, $class->id, $section->id);
    expect($initialRoll)->toBe('1');

    // Test suggested roll number endpoint
    $response = $this->actingAs($adminUser, 'admin')->getJson(route('admin.students.suggested-roll-number', [
        'academic_session_id' => $session->id,
        'class_id' => $class->id,
        'section_id' => $section->id,
    ]));

    $response->assertStatus(200)
        ->assertJson([
            'status' => true,
            'suggested_roll_number' => '1',
        ]);

    // Create a student with roll number '1'
    $student1 = $studentService->create([
        'name' => 'Student One',
        'email' => 'student1_'.uniqid().'@test.com',
        'password' => 'password123',
        'admission_no' => 'ADM-'.uniqid(),
        'roll_number' => '1',
        'academic_session_id' => $session->id,
        'class_id' => $class->id,
        'section_id' => $section->id,
        'status' => 1,
    ]);

    expect($student1)->toBeInstanceOf(StudentProfile::class);

    // Next suggested roll number should now be '2'
    $nextRoll = $studentService->generateSuggestedRollNumber($session->id, $class->id, $section->id);
    expect($nextRoll)->toBe('2');

    // Trying to create another student with duplicate roll_number '1' in same session, class, section via request validation should fail
    $duplicateData = [
        'name' => 'Student Two Duplicate',
        'email' => 'student2_'.uniqid().'@test.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'admission_no' => 'ADM-'.uniqid(),
        'roll_number' => '1', // Duplicate roll number
        'academic_session_id' => $session->id,
        'class_id' => $class->id,
        'section_id' => $section->id,
        'status' => 1,
    ];

    $postResponse = $this->actingAs($adminUser, 'admin')->postJson(route('admin.students.store'), $duplicateData);
    $postResponse->assertStatus(422)
        ->assertJsonValidationErrors(['roll_number']);
});
