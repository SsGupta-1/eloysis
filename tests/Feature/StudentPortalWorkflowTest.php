<?php

use App\Models\AcademicClass;
use App\Models\AcademicSession;
use App\Models\FeeHead;
use App\Models\Role;
use App\Models\Section;
use App\Models\StudentEnrollment;
use App\Models\StudentFeeAllocation;
use App\Models\StudentProfile;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

test('student portal authentication and navigation workflow works correctly', function () {
    // 1. Setup Student Role
    $studentRole = Role::firstOrCreate(
        ['slug' => 'student'],
        ['role_name' => 'Student', 'status' => 1]
    );

    // 2. Setup Session, Class, Section
    $session = AcademicSession::firstOrCreate(
        ['name' => '2026-2027'],
        ['start_date' => '2026-04-01', 'end_date' => '2027-03-31', 'status' => 1]
    );

    $class = AcademicClass::firstOrCreate(
        ['class_name' => 'Class 10th Test'],
        ['class_code' => 'CLS10TEST', 'status' => 1]
    );

    $section = Section::firstOrCreate(
        ['name' => 'Section A Test'],
        ['code' => 'SECTA', 'status' => 1]
    );

    // 3. Create Student User & Profile
    $admNo = 'TESTADM'.rand(1000, 9999);
    $studentUser = User::create([
        'name' => 'Rohit Sharma',
        'email' => 'rohit_'.uniqid().'@test.com',
        'mobile_number' => '98765'.rand(10000, 99999),
        'password' => Hash::make('password123'),
        'role_id' => $studentRole->id,
        'status' => 1,
    ]);

    $studentProfile = StudentProfile::create([
        'user_id' => $studentUser->id,
        'admission_no' => $admNo,
        'father_name' => 'Suresh Sharma',
        'mother_name' => 'Sunita Sharma',
        'gender' => 'male',
        'dob' => '2010-05-15',
        'address' => '123 Test Street, New Delhi',
    ]);

    $enrollment = StudentEnrollment::create([
        'user_id' => $studentUser->id,
        'stu_profile_id' => $studentProfile->id,
        'class_id' => $class->id,
        'section_id' => $section->id,
        'academic_session_id' => $session->id,
        'roll_number' => 15,
        'status' => 1,
    ]);

    // 4. Guest is redirected from protected student routes to student login
    $this->get('/student/dashboard')
        ->assertRedirect('/student/login');

    // 5. Check student login page loads
    $this->get('/student/login')
        ->assertStatus(200)
        ->assertSee('Student Portal');

    // 6. Test Login via Admission Number
    $loginResponse = $this->post('/student/login', [
        'login' => $admNo,
        'password' => 'password123',
    ]);

    $loginResponse->assertRedirect('/student/dashboard');
    $this->assertAuthenticatedAs($studentUser, 'student');

    // 7. Verify Student Portal Pages render for authenticated student
    $this->get('/student/dashboard')
        ->assertStatus(200)
        ->assertSee('Rohit Sharma')
        ->assertSee($admNo);

    $this->get('/student/profile')
        ->assertStatus(200)
        ->assertSee('Suresh Sharma')
        ->assertSee('Personal Information');

    $this->get('/student/timetable')
        ->assertStatus(200)
        ->assertSee('Class Timetable');

    $this->get('/student/attendance')
        ->assertStatus(200)
        ->assertSee('Attendance Record');

    $this->get('/student/exams')
        ->assertStatus(200)
        ->assertSee('Examinations');

    $this->get('/student/results')
        ->assertStatus(200)
        ->assertSee('Academic Results');

    // Create a fee head and allocation with string month
    $feeHead = FeeHead::create([
        'name' => 'Tuition Fee Head '.uniqid(),
        'code' => 'TF_'.uniqid(),
        'is_active' => true,
    ]);

    StudentFeeAllocation::create([
        'student_enrollment_id' => $enrollment->id,
        'stu_profile_id' => $studentProfile->id,
        'academic_session_id' => $session->id,
        'fee_head_id' => $feeHead->id,
        'title' => 'Tuition Fee October',
        'month' => 'October',
        'year' => 2026,
        'due_date' => now()->addDays(10),
        'amount' => 1500.00,
        'discount_amount' => 100.00,
        'fine_amount' => 0.00,
        'paid_amount' => 500.00,
        'status' => 'partial',
    ]);

    $this->get('/student/fees')
        ->assertStatus(200)
        ->assertSee('Fee Records')
        ->assertSee('Period: October 2026')
        ->assertSee($feeHead->name);

    // 8. Test Password Change
    $passwordResponse = $this->post('/student/profile/password', [
        'current_password' => 'password123',
        'password' => 'newpassword123',
        'password_confirmation' => 'newpassword123',
    ]);

    $passwordResponse->assertSessionHas('success');
    expect(Hash::check('newpassword123', $studentUser->fresh()->password))->toBeTrue();

    // 9. Test Logout
    $logoutResponse = $this->post('/student/logout');
    $logoutResponse->assertRedirect('/student/login');
    expect(Auth::guard('student')->check())->toBeFalse();

    // 10. Test Login via Email with new password
    $this->post('/student/login', [
        'login' => $studentUser->email,
        'password' => 'newpassword123',
    ])->assertRedirect('/student/dashboard');

    expect(Auth::guard('student')->check())->toBeTrue();
});
