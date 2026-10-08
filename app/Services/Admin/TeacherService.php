<?php

namespace App\Services\Admin;

use App\Helpers\UploadHelper;
use App\Models\Permission;
use App\Models\Role;
use App\Models\TeacherProfile;
use App\Repositories\Admin\TeacherRepository;
use App\Repositories\Admin\UserRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class TeacherService
{
    protected TeacherRepository $teacherRepository;

    protected UserRepository $userRepository;

    public function __construct(
        TeacherRepository $teacherRepository,
        UserRepository $userRepository
    ) {
        $this->teacherRepository = $teacherRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * Get teacher list
     */
    public function getList(
        array $filters = [],
        int $perPage = 10,
        int $page = 1,
        ?int $orderColumn = null,
        string $orderDirection = 'asc'
    ) {
        return $this->teacherRepository->getList($filters, $perPage, $page, $orderColumn, $orderDirection);
    }

    /**
     * Get teacher details
     */
    public function find(int $id)
    {
        return $this->teacherRepository->findWithUser($id);
    }

    /**
     * Create teacher
     */
    public function create(array $data)
    {
        return DB::transaction(function () use ($data) {

            $teacherRole = Role::where(
                'slug',
                'teacher'
            )->firstOrFail();

            /*
            |--------------------------------------------------------------------------
            | Create User
            |--------------------------------------------------------------------------
            */

            if (isset($data['profile_image'])) {
                $data['profile_image'] = UploadHelper::upload(
                    $data['profile_image'],
                    'assets/uploads/teachers'
                );

            }

            $user = $this->userRepository->create([

                'name' => $data['name'],

                'email' => $data['email'],

                'mobile' => $data['mobile'] ?? null,

                'password' => Hash::make(
                    $data['password']
                ),

                'role_id' => $teacherRole->id,

                'status' => $data['status'],
                'created_by' => Auth::guard('admin')->id(),
                'profile_image' => $data['profile_image'] ?? null,

            ]);

            /*
            |--------------------------------------------------------------------------
            | Create Teacher Profile
            |--------------------------------------------------------------------------
            */

            return $this->teacherRepository->createProfile(
                $user->id,
                $data
            );
        });
    }

    /**
     * Update teacher
     */
    public function update(
        int $id,
        array $data
    ) {
        return DB::transaction(function () use (
            $id,
            $data
        ) {

            $teacher =
                $this->teacherRepository->findWithUser($id);

            /*
            |--------------------------------------------------------------------------
            | Update User
            |--------------------------------------------------------------------------
            */

            if (isset($data['profile_image'])) {

                $data['profile_image'] = UploadHelper::replace(

                    $data['profile_image'],

                    $teacher->user->profile_image,

                    'assets/uploads/teachers'
                );

            }

            $userData = [

                'name' => $data['name'],

                'email' => $data['email'],

                'mobile' => $data['mobile'] ?? null,

                'status' => $data['status'],

                'profile_image' => $data['profile_image'] ?? $teacher->user->profile_image,
                'updated_by' => Auth::guard('admin')->id(),
            ];

            if (! empty($data['password'])) {

                $userData['password'] =
                    Hash::make(
                        $data['password']
                    );
            }

            $this->userRepository->update(
                $teacher->user_id,
                $userData
            );

            /*
            |--------------------------------------------------------------------------
            | Update Teacher Profile
            |--------------------------------------------------------------------------
            */

            $this->teacherRepository->updateProfile(
                $teacher->id,
                $data
            );

            return $this->teacherRepository
                ->findWithUser($teacher->id);
        });
    }

    /**
     * Delete teacher
     */
    public function delete(int $id): bool
    {
        return DB::transaction(function () use ($id) {

            $teacher =
                $this->teacherRepository->findWithUser($id);

            /*
            |--------------------------------------------------------------------------
            | Delete User
            |--------------------------------------------------------------------------
            */
            UploadHelper::delete(
                $teacher->user->profile_image
            );

            $this->userRepository->delete(
                $teacher->user_id
            );

            /*
            |--------------------------------------------------------------------------
            | Delete Teacher Profile
            |--------------------------------------------------------------------------
            */

            $this->teacherRepository->delete(
                $teacher->id
            );

            return true;
        });
    }

    /**
     * Change teacher status
     */
    public function changeStatus(
        int $id
    ) {

        $teacher =
            $this->teacherRepository->findWithUser($id);

        return $this->userRepository->changeStatus(
            $teacher->user_id,
        );
    }

    /**
     * Get teacher user permissions and role baseline
     */
    public function getPermissionsData(TeacherProfile $teacher): array
    {
        $user = $teacher->user;
        $user->load(['role.permissions', 'permissions']);

        $role = $user->role;
        $rolePermissionIds = $role ? $role->permissions->pluck('id')->toArray() : [];
        $directPermissionIds = $user->permissions->pluck('id')->toArray();

        $permissions = Permission::active()
            ->orderBy('module')
            ->orderBy('name')
            ->get()
            ->groupBy('module');

        return [
            'profile' => $teacher,
            'teacher' => $teacher,
            'user' => $user,
            'role' => $role,
            'has_custom_permissions' => (bool) $user->has_custom_permissions,
            'role_permission_ids' => $rolePermissionIds,
            'direct_permission_ids' => $directPermissionIds,
            'grouped_permissions' => $permissions,
        ];
    }

    /**
     * Update teacher user-level custom permissions
     */
    public function updatePermissions(TeacherProfile $teacher, bool $hasCustomPermissions, array $permissionIds): bool
    {
        $user = $teacher->user;

        if ($hasCustomPermissions) {
            $user->syncCustomPermissions($permissionIds);
        } else {
            $user->resetToRolePermissions();
        }

        return true;
    }
}
