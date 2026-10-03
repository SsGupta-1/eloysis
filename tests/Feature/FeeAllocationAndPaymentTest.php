<?php

use App\Models\AcademicClass;
use App\Models\AcademicSession;
use App\Models\FeeHead;
use App\Models\FeePayment;
use App\Models\FeeStructure;
use App\Models\Role;
use App\Models\Section;
use App\Models\StudentEnrollment;
use App\Models\StudentFeeAllocation;
use App\Models\StudentProfile;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

test('student fee allocation and payment cashier workflow', function () {
    // 1. Setup Admin user
    $adminRole = Role::firstOrCreate(
        ['slug' => 'super_admin'],
        ['role_name' => 'Super Admin', 'status' => 1]
    );

    $adminUser = User::firstOrCreate(
        ['email' => 'admin_fee_pos_test@test.com'],
        [
            'name' => 'Admin Fee POS Tester',
            'mobile' => '98765'.rand(10000, 99999),
            'password' => Hash::make('password'),
            'role_id' => $adminRole->id,
            'status' => 1,
        ]
    );

    $this->actingAs($adminUser, 'admin');

    // 2. Setup Session, Class, Section, Student & Enrollment
    $session = AcademicSession::firstOrCreate(
        ['name' => '2026-2027'],
        ['start_date' => '2026-04-01', 'end_date' => '2027-03-31', 'status' => 1, 'is_current' => 1]
    );

    $class = AcademicClass::create([
        'class_name' => 'Class 10th POS '.uniqid(),
        'class_code' => 'C10-'.uniqid(),
        'status' => 1,
        'sort_order' => 10,
    ]);

    $section = Section::firstOrCreate(
        ['name' => 'A'],
        ['status' => 1]
    );

    $studentUser = User::create([
        'name' => 'Rahul Sharma POS',
        'email' => 'rahul_pos_'.uniqid().'@test.com',
        'mobile' => '98'.rand(10000000, 99999999),
        'password' => Hash::make('password'),
        'role_id' => $adminRole->id,
        'status' => 1,
    ]);

    $studentProfile = StudentProfile::create([
        'user_id' => $studentUser->id,
        'admission_no' => 'ADM-'.rand(1000, 9999),
        'admission_date' => '2026-04-01',
        'father_name' => 'Vijay Sharma',
        'guardian_mobile' => '9876543210',
    ]);

    $enrollment = StudentEnrollment::create([
        'user_id' => $studentUser->id,
        'stu_profile_id' => $studentProfile->id,
        'academic_session_id' => $session->id,
        'class_id' => $class->id,
        'section_id' => $section->id,
        'roll_number' => '101',
        'status' => 1,
    ]);

    // 3. Setup Fee Heads & Structures
    $tuitionHead = FeeHead::create([
        'name' => 'Tuition Fee '.uniqid(),
        'code' => 'TUI_'.rand(100, 999),
        'description' => 'Tuition',
        'is_active' => true,
    ]);

    $examHead = FeeHead::create([
        'name' => 'Exam Fee '.uniqid(),
        'code' => 'EXM_'.rand(100, 999),
        'description' => 'Exam Fee',
        'is_active' => true,
    ]);

    $tuitionStructure = FeeStructure::create([
        'academic_session_id' => $session->id,
        'academic_class_id' => $class->id,
        'fee_head_id' => $tuitionHead->id,
        'amount' => 2500.00,
        'frequency' => 'monthly',
        'due_date' => '2026-04-10',
        'fine_type' => 'flat',
        'fine_amount' => 50.00,
        'is_active' => true,
    ]);

    // 4. Test Bulk Fee Allocation
    $bulkData = [
        'academic_session_id' => $session->id,
        'academic_class_id' => $class->id,
        'section_id' => $section->id,
        'fee_structure_ids' => [$tuitionStructure->id],
        'month' => 'April',
        'year' => 2026,
        'due_date' => '2026-04-10',
    ];

    $bulkRes = $this->postJson(route('admin.fees.allocations.bulk'), $bulkData);
    $bulkRes->assertOk()->assertJson(['status' => true]);

    $allocation = StudentFeeAllocation::where('student_enrollment_id', $enrollment->id)->first();
    expect($allocation)->not->toBeNull();
    expect((float) $allocation->amount)->toBe(2500.00);
    expect($allocation->status)->toBe('unpaid');

    // 5. Test Single / Custom Fee Allocation
    $singleData = [
        'student_enrollment_id' => $enrollment->id,
        'academic_session_id' => $session->id,
        'fee_head_id' => $examHead->id,
        'title' => 'Term 1 Exam Fee',
        'month' => 'September',
        'year' => 2026,
        'due_date' => '2026-09-15',
        'amount' => 500.00,
        'discount_amount' => 50.00,
        'fine_amount' => 0.00,
    ];

    $singleRes = $this->postJson(route('admin.fees.allocations.store'), $singleData);
    $singleRes->assertOk()->assertJson(['status' => true]);

    $examAllocation = StudentFeeAllocation::where('fee_head_id', $examHead->id)->first();
    expect($examAllocation)->not->toBeNull();
    expect((float) $examAllocation->amount)->toBe(500.00);
    expect((float) $examAllocation->discount_amount)->toBe(50.00);

    // 6. Test Fee Allocations List & Search
    $listAllocRes = $this->getJson(route('admin.fees.allocations.list'));
    $listAllocRes->assertOk()->assertJsonStructure(['data', 'recordsTotal']);

    // 7. Test Student Ledger Endpoint for Cashier POS
    $ledgerRes = $this->getJson(route('admin.fees.payments.ledger', ['student_enrollment_id' => $enrollment->id]));
    $ledgerRes->assertOk()->assertJsonPath('status', true);
    expect($ledgerRes->json('data.student.name'))->toBe('Rahul Sharma POS');
    expect(count($ledgerRes->json('data.dues')))->toBe(2);

    // 8. Test Collecting Payment (Partial / Full payment on allocations)
    $paymentData = [
        'student_enrollment_id' => $enrollment->id,
        'payment_date' => '2026-04-05',
        'payment_mode' => 'cash',
        'remarks' => 'Cash received at fee counter',
        'items' => [
            [
                'allocation_id' => $allocation->id,
                'amount_paid' => 2500.00,
                'discount_applied' => 0,
                'fine_paid' => 0,
            ],
            [
                'allocation_id' => $examAllocation->id,
                'amount_paid' => 450.00, // (500 - 50 discount = 450)
                'discount_applied' => 50.00,
                'fine_paid' => 0,
            ],
        ],
    ];

    $payRes = $this->postJson(route('admin.fees.payments.store'), $paymentData);
    $payRes->assertOk()->assertJson(['status' => true]);
    $paymentId = $payRes->json('data.payment_id');
    expect($paymentId)->not->toBeNull();

    $payment = FeePayment::find($paymentId);
    expect($payment)->not->toBeNull();
    expect((float) $payment->total_paid)->toBe(2950.00);
    expect($payment->receipt_no)->toStartWith('REC-');

    // Verify allocations are marked paid
    expect($allocation->fresh()->status)->toBe('paid');
    expect((float) $allocation->fresh()->paid_amount)->toBe(2500.00);
    expect($examAllocation->fresh()->status)->toBe('paid');
    expect((float) $examAllocation->fresh()->paid_amount)->toBe(450.00);

    // 9. Test Payment History List & Print
    $listPayRes = $this->getJson(route('admin.fees.payments.list'));
    $listPayRes->assertOk()->assertJsonStructure(['data', 'recordsTotal']);

    $printRes = $this->get(route('admin.fees.payments.print', $payment->id));
    $printRes->assertOk()->assertSee($payment->receipt_no);

    // 10. Test Void / Cancel Payment
    $cancelRes = $this->postJson(route('admin.fees.payments.cancel', $payment->id), [
        'reason' => 'Cheque bounced / Test cancellation',
    ]);
    $cancelRes->assertOk()->assertJson(['status' => true]);

    expect($payment->fresh()->status)->toBe('cancelled');
    // Allocations balance should be restored to unpaid
    expect($allocation->fresh()->status)->toBe('unpaid');
    expect((float) $allocation->fresh()->paid_amount)->toBe(0.00);
});
