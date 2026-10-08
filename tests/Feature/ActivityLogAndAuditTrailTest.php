<?php

use App\Models\ActivityLog;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

test('activity logger middleware logs mutating actions to database and masks sensitive fields', function () {
    // 1. Setup Role & User
    $adminRole = Role::firstOrCreate(
        ['slug' => 'super_admin'],
        ['role_name' => 'Super Admin', 'status' => 1]
    );

    $user = User::create([
        'name' => 'Audit Admin',
        'email' => 'audit_'.uniqid().'@test.com',
        'password' => Hash::make('password123'),
        'role_id' => $adminRole->id,
        'status' => 1,
    ]);

    // Ensure permissions
    Permission::firstOrCreate(['slug' => 'activity_logs.view'], ['name' => 'View Activity Audit Logs', 'module' => 'activity_logs', 'status' => 1]);
    Permission::firstOrCreate(['slug' => 'activity_logs.delete'], ['name' => 'Delete Activity Audit Logs', 'module' => 'activity_logs', 'status' => 1]);
    Permission::firstOrCreate(['slug' => 'roles.create'], ['name' => 'Create Roles', 'module' => 'roles', 'status' => 1]);

    $initialCount = ActivityLog::count();

    // 2. Perform a POST request (Create Role)
    $response = $this->actingAs($user, 'admin')
        ->post(route('admin.roles.store'), [
            'role_name' => 'Test Audit Role '.uniqid(),
            'slug' => 'test_audit_role_'.uniqid(),
            'description' => 'Role for audit test',
            'status' => 1,
            'password' => 'secret_password_123', // Sensitive field should be masked
        ]);

    expect($response->status())->toBeIn([200, 302]);

    // 3. Verify that ActivityLog was created
    expect(ActivityLog::count())->toBeGreaterThan($initialCount);

    $latestLog = ActivityLog::latest('id')->first();
    expect($latestLog)->not->toBeNull();
    expect($latestLog->user_id)->toBe($user->id);
    expect($latestLog->module)->toBe('roles');
    expect($latestLog->action)->toBe('CREATE');
    expect($latestLog->method)->toBe('POST');

    // 4. Verify password masking
    if (! empty($latestLog->payload)) {
        expect($latestLog->payload)->toHaveKey('password');
        expect($latestLog->payload['password'])->toBe('********');
    }

    // 5. Verify GET request does NOT create an ActivityLog in DB
    $countBeforeGet = ActivityLog::count();
    $this->actingAs($user, 'admin')->get(route('admin.activity-logs.index'))->assertOk();
    expect(ActivityLog::count())->toBe($countBeforeGet);

    // 6. Test Activity Logs AJAX List DataTable endpoint
    $listResponse = $this->actingAs($user, 'admin')
        ->getJson(route('admin.activity-logs.list', [
            'draw' => 1,
            'start' => 0,
            'length' => 10,
            'module' => 'roles',
        ]))
        ->assertOk()
        ->json();

    expect($listResponse)->toHaveKey('data');
    expect($listResponse['recordsTotal'])->toBeGreaterThan(0);

    // 7. Test Show Single Log Detail
    $showResponse = $this->actingAs($user, 'admin')
        ->getJson(route('admin.activity-logs.show', $latestLog->id))
        ->assertOk()
        ->json();

    expect($showResponse['status'])->toBeTrue();
    expect($showResponse['data']['log']['id'])->toBe($latestLog->id);

    // 8. Test Clear Old Logs
    $clearResponse = $this->actingAs($user, 'admin')
        ->postJson(route('admin.activity-logs.clear'), [
            'days' => 30,
        ])
        ->assertOk()
        ->json();

    expect($clearResponse['status'])->toBeTrue();
});
