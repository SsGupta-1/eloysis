<?php

use App\Models\Permission;
use App\Models\Role;
use App\Models\StaffProfile;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

test('rbac and user-level permission customization system works as expected', function () {
    // 1. Setup Permissions
    $permViewUsers = Permission::firstOrCreate(['slug' => 'admins.view'], ['name' => 'View Admin Users', 'module' => 'admins', 'status' => 1]);
    $permCreateUsers = Permission::firstOrCreate(['slug' => 'admins.create'], ['name' => 'Create Admin Users', 'module' => 'admins', 'status' => 1]);
    $permEditUsers = Permission::firstOrCreate(['slug' => 'admins.edit'], ['name' => 'Edit Admin Users', 'module' => 'admins', 'status' => 1]);
    $permDeleteUsers = Permission::firstOrCreate(['slug' => 'admins.delete'], ['name' => 'Delete Admin Users', 'module' => 'admins', 'status' => 1]);
    $permDashboard = Permission::firstOrCreate(['slug' => 'dashboard.view'], ['name' => 'View Dashboard', 'module' => 'dashboard', 'status' => 1]);
    $permLogs = Permission::firstOrCreate(['slug' => 'logs.view'], ['name' => 'View System Logs', 'module' => 'logs', 'status' => 1]);

    // 2. Setup Super Admin & Admin Roles
    $superAdminRole = Role::firstOrCreate(
        ['slug' => 'super_admin'],
        ['role_name' => 'Super Admin', 'status' => 1]
    );

    $adminRole = Role::firstOrCreate(
        ['slug' => 'admin'],
        ['role_name' => 'Admin', 'status' => 1]
    );

    // Assign baseline permissions to Admin Role (View, Create, Edit, Delete, Dashboard, Logs)
    $adminRole->permissions()->sync([
        $permViewUsers->id,
        $permCreateUsers->id,
        $permEditUsers->id,
        $permDeleteUsers->id,
        $permDashboard->id,
        $permLogs->id,
    ]);

    // 3. Super Admin User
    $superAdmin = User::create([
        'name' => 'Super Admin User',
        'email' => 'superadmin_'.uniqid().'@test.com',
        'password' => Hash::make('password'),
        'role_id' => $superAdminRole->id,
        'status' => 1,
    ]);

    // Super Admin has all permissions unconditionally
    expect($superAdmin->hasPermission('admins.delete'))->toBeTrue();
    expect($superAdmin->hasPermission('dashboard.view'))->toBeTrue();
    expect($superAdmin->hasPermission('non_existent.permission'))->toBeTrue();

    // 4. Admin User 1 (Default Role Inheritance - Full Admin access per role)
    $adminUser1 = User::create([
        'name' => 'Admin User 1',
        'email' => 'admin1_'.uniqid().'@test.com',
        'password' => Hash::make('password'),
        'role_id' => $adminRole->id,
        'has_custom_permissions' => false,
        'status' => 1,
    ]);

    expect($adminUser1->hasPermission('admins.view'))->toBeTrue();
    expect($adminUser1->hasPermission('admins.create'))->toBeTrue();
    expect($adminUser1->hasPermission('admins.edit'))->toBeTrue();
    expect($adminUser1->hasPermission('admins.delete'))->toBeTrue();
    expect($adminUser1->hasPermission('dashboard.view'))->toBeTrue();

    // 5. Admin User 2 (Custom: Users View, Create, Edit -> No Delete)
    $adminUser2 = User::create([
        'name' => 'Admin User 2',
        'email' => 'admin2_'.uniqid().'@test.com',
        'password' => Hash::make('password'),
        'role_id' => $adminRole->id,
        'has_custom_permissions' => false,
        'status' => 1,
    ]);

    $adminUser2->syncCustomPermissions([
        $permViewUsers->id,
        $permCreateUsers->id,
        $permEditUsers->id,
    ]);

    expect($adminUser2->fresh()->has_custom_permissions)->toBeTrue();
    expect($adminUser2->hasPermission('admins.view'))->toBeTrue();
    expect($adminUser2->hasPermission('admins.create'))->toBeTrue();
    expect($adminUser2->hasPermission('admins.edit'))->toBeTrue();
    expect($adminUser2->hasPermission('admins.delete'))->toBeFalse(); // Overridden to false!
    expect($adminUser2->hasPermission('dashboard.view'))->toBeFalse();

    // 6. Admin User 3 (Custom: Logs View, Users View -> No User Create/Edit/Delete)
    $adminUser3 = User::create([
        'name' => 'Admin User 3',
        'email' => 'admin3_'.uniqid().'@test.com',
        'password' => Hash::make('password'),
        'role_id' => $adminRole->id,
        'has_custom_permissions' => false,
        'status' => 1,
    ]);

    $adminUser3->syncCustomPermissions([
        $permLogs->id,
        $permViewUsers->id,
    ]);

    expect($adminUser3->hasPermission('logs.view'))->toBeTrue();
    expect($adminUser3->hasPermission('admins.view'))->toBeTrue();
    expect($adminUser3->hasPermission('admins.create'))->toBeFalse();
    expect($adminUser3->hasPermission('admins.edit'))->toBeFalse();
    expect($adminUser3->hasPermission('admins.delete'))->toBeFalse();

    // 7. Admin User 4 (Custom: Dashboard View, Logs View only)
    $adminUser4 = User::create([
        'name' => 'Admin User 4',
        'email' => 'admin4_'.uniqid().'@test.com',
        'password' => Hash::make('password'),
        'role_id' => $adminRole->id,
        'has_custom_permissions' => false,
        'status' => 1,
    ]);

    $adminUser4->syncCustomPermissions([
        $permDashboard->id,
        $permLogs->id,
    ]);

    expect($adminUser4->hasPermission('dashboard.view'))->toBeTrue();
    expect($adminUser4->hasPermission('logs.view'))->toBeTrue();
    expect($adminUser4->hasPermission('admins.view'))->toBeFalse();
    expect($adminUser4->hasPermission('admins.create'))->toBeFalse();

    // 8. Test Reverting back to Role Defaults
    $adminUser4->resetToRolePermissions();
    $adminUser4Fresh = $adminUser4->fresh();

    expect($adminUser4Fresh->has_custom_permissions)->toBeFalse();
    // Inherits role defaults again!
    expect($adminUser4Fresh->hasPermission('admins.view'))->toBeTrue();
    expect($adminUser4Fresh->hasPermission('admins.create'))->toBeTrue();
    expect($adminUser4Fresh->hasPermission('admins.delete'))->toBeTrue();

    // 9. Test Inactive account blocks all permissions
    $adminUser1->update(['status' => 0]);
    expect($adminUser1->fresh()->hasPermission('dashboard.view'))->toBeFalse();
});

test('role and staff permission ajax endpoints function correctly', function () {
    $superAdminRole = Role::firstOrCreate(['slug' => 'super_admin'], ['role_name' => 'Super Admin', 'status' => 1]);
    $adminRole = Role::firstOrCreate(['slug' => 'admin'], ['role_name' => 'Admin', 'status' => 1]);

    $superAdmin = User::create([
        'name' => 'Super Admin Tester',
        'email' => 'super_api_'.uniqid().'@test.com',
        'password' => Hash::make('password'),
        'role_id' => $superAdminRole->id,
        'status' => 1,
    ]);

    $staffUser = User::create([
        'name' => 'Staff Member',
        'email' => 'staff_api_'.uniqid().'@test.com',
        'password' => Hash::make('password'),
        'role_id' => $adminRole->id,
        'status' => 1,
    ]);

    $staff = StaffProfile::create([
        'user_id' => $staffUser->id,
        'employee_id' => 'EMP-'.rand(1000, 9999),
        'designation' => 'Manager',
        'department' => 'Administration',
    ]);

    $perm1 = Permission::firstOrCreate(['slug' => 'dashboard.view'], ['name' => 'View Dashboard', 'module' => 'dashboard', 'status' => 1]);
    $perm2 = Permission::firstOrCreate(['slug' => 'admins.view'], ['name' => 'View Admin Users', 'module' => 'admins', 'status' => 1]);

    // Test Role Permissions Update via API
    $response = $this->actingAs($superAdmin, 'admin')
        ->postJson(route('admin.roles.permissions.update', $adminRole->id), [
            'permissions' => [$perm1->id, $perm2->id],
        ]);

    $response->assertOk();
    expect($adminRole->fresh()->permissions->pluck('id')->toArray())->toContain($perm1->id, $perm2->id);

    // Test Staff Permissions Get via API
    $response = $this->actingAs($superAdmin, 'admin')
        ->getJson(route('admin.staffs.permissions', $staff->id));

    $response->assertOk()
        ->assertJsonStructure([
            'status',
            'data' => [
                'user',
                'role',
                'has_custom_permissions',
                'role_permission_ids',
                'direct_permission_ids',
                'grouped_permissions',
            ],
        ]);

    // Test Staff Custom Permissions Update via API
    $response = $this->actingAs($superAdmin, 'admin')
        ->postJson(route('admin.staffs.permissions.update', $staff->id), [
            'has_custom_permissions' => 1,
            'permissions' => [$perm1->id],
        ]);

    $response->assertOk();
    expect($staffUser->fresh()->has_custom_permissions)->toBeTrue();
    expect($staffUser->fresh()->permissions->pluck('id')->toArray())->toEqual([$perm1->id]);
});
