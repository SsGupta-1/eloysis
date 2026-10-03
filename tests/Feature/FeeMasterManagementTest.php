<?php

use App\Models\AcademicClass;
use App\Models\AcademicSession;
use App\Models\FeeDiscount;
use App\Models\FeeHead;
use App\Models\FeeStructure;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

test('fee master management (heads, discounts, structures) full workflow', function () {
    // 1. Setup Admin user
    $adminRole = Role::firstOrCreate(
        ['slug' => 'super_admin'],
        ['role_name' => 'Super Admin', 'status' => 1]
    );

    $adminUser = User::firstOrCreate(
        ['email' => 'admin_fee_master_test@test.com'],
        [
            'name' => 'Admin Fee Tester',
            'mobile' => '98765'.rand(10000, 99999),
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
        'class_name' => 'Class 9th Fee Test '.uniqid(),
        'class_code' => 'C9-'.uniqid(),
        'status' => 1,
        'sort_order' => 9,
    ]);

    $this->actingAs($adminUser, 'admin');

    // 2. Fee Head Tests
    // Index view
    $response = $this->get(route('admin.fees.heads.index'));
    $response->assertOk();

    // Store Fee Head
    $headData = [
        'name' => 'Tuition Fee '.uniqid(),
        'code' => 'TUI_'.rand(100, 999),
        'description' => 'Standard monthly tuition fee',
        'is_active' => 1,
    ];

    $storeHeadRes = $this->postJson(route('admin.fees.heads.store'), $headData);
    $storeHeadRes->assertOk()->assertJson(['status' => true]);

    $feeHead = FeeHead::where('code', $headData['code'])->first();
    expect($feeHead)->not->toBeNull();
    expect($feeHead->name)->toBe($headData['name']);

    // List Fee Heads (DataTable)
    $listHeadRes = $this->getJson(route('admin.fees.heads.list'));
    $listHeadRes->assertOk()->assertJsonStructure(['data', 'recordsTotal']);

    // Edit & Update Fee Head
    $editHeadRes = $this->getJson(route('admin.fees.heads.edit', $feeHead->id));
    $editHeadRes->assertOk()->assertJsonPath('data.name', $feeHead->name);

    $updateHeadRes = $this->putJson(route('admin.fees.heads.update', $feeHead->id), [
        'name' => 'Updated Tuition Fee',
        'code' => $headData['code'],
        'description' => 'Updated description',
        'is_active' => 1,
    ]);
    $updateHeadRes->assertOk()->assertJson(['status' => true]);
    expect($feeHead->fresh()->name)->toBe('Updated Tuition Fee');

    // Toggle Status
    $statusHeadRes = $this->patchJson(route('admin.fees.heads.status', $feeHead->id));
    $statusHeadRes->assertOk()->assertJson(['status' => true]);
    expect($feeHead->fresh()->is_active)->toBeFalse();

    // 3. Fee Discount Tests
    $response = $this->get(route('admin.fees.discounts.index'));
    $response->assertOk();

    $discountData = [
        'name' => 'Sibling Concession '.uniqid(),
        'code' => 'SIB_'.rand(100, 999),
        'discount_type' => 'percentage',
        'amount' => 15,
        'description' => '15% discount for younger sibling',
        'is_active' => 1,
    ];

    $storeDiscountRes = $this->postJson(route('admin.fees.discounts.store'), $discountData);
    $storeDiscountRes->assertOk()->assertJson(['status' => true]);

    $discount = FeeDiscount::where('code', $discountData['code'])->first();
    expect($discount)->not->toBeNull();
    expect((float) $discount->amount)->toBe(15.0);

    // List Fee Discounts
    $listDiscountRes = $this->getJson(route('admin.fees.discounts.list'));
    $listDiscountRes->assertOk()->assertJsonStructure(['data', 'recordsTotal']);

    // Update Fee Discount
    $updateDiscountRes = $this->putJson(route('admin.fees.discounts.update', $discount->id), [
        'name' => 'Updated Sibling Concession',
        'code' => $discountData['code'],
        'discount_type' => 'percentage',
        'amount' => 20,
        'description' => '20% discount',
        'is_active' => 1,
    ]);
    $updateDiscountRes->assertOk()->assertJson(['status' => true]);
    expect((float) $discount->fresh()->amount)->toBe(20.0);

    // 4. Fee Structure Tests
    $response = $this->get(route('admin.fees.structures.index'));
    $response->assertOk();

    $structureData = [
        'academic_session_id' => $session->id,
        'academic_class_id' => $class->id,
        'fee_head_id' => $feeHead->id,
        'amount' => 3500.00,
        'frequency' => 'monthly',
        'due_date' => '2026-04-10',
        'fine_type' => 'flat',
        'fine_amount' => 50.00,
        'is_active' => 1,
    ];

    $storeStructRes = $this->postJson(route('admin.fees.structures.store'), $structureData);
    $storeStructRes->assertOk()->assertJson(['status' => true]);

    $structure = FeeStructure::where('academic_class_id', $class->id)
        ->where('fee_head_id', $feeHead->id)
        ->first();
    expect($structure)->not->toBeNull();
    expect((float) $structure->amount)->toBe(3500.00);

    // List Fee Structures
    $listStructRes = $this->getJson(route('admin.fees.structures.list'));
    $listStructRes->assertOk()->assertJsonStructure(['data', 'recordsTotal']);

    // Update Fee Structure
    $updateStructRes = $this->putJson(route('admin.fees.structures.update', $structure->id), [
        'academic_session_id' => $session->id,
        'academic_class_id' => $class->id,
        'fee_head_id' => $feeHead->id,
        'amount' => 4000.00,
        'frequency' => 'monthly',
        'due_date' => '2026-04-15',
        'fine_type' => 'flat',
        'fine_amount' => 100.00,
        'is_active' => 1,
    ]);
    $updateStructRes->assertOk()->assertJson(['status' => true]);
    expect((float) $structure->fresh()->amount)->toBe(4000.00);

    // Delete Fee Structure
    $deleteStructRes = $this->deleteJson(route('admin.fees.structures.destroy', $structure->id));
    $deleteStructRes->assertOk()->assertJson(['status' => true]);
    expect(FeeStructure::find($structure->id))->toBeNull();
});
