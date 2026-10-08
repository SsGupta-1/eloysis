<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Helpers\UploadHelper;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'mobile_verified_at' => 'datetime',
            'password' => 'hashed',
            'status' => 'boolean',
            'has_custom_permissions' => 'boolean',
            'last_login_at' => 'datetime',
        ];
    }

    protected $fillable = [
        'role_id',
        'name',
        'username',
        'email',
        'mobile',
        'password',
        'profile_image',
        'status',
        'has_custom_permissions',
        'last_login_at',
        'created_by',
        'updated_by',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $appends = [
        'profile_image_url',
    ];

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function scopeStudents($query)
    {
        return $query->whereHas('role', function ($q) {
            $q->where('slug', 'student');
        });
    }

    public function getProfileImageUrlAttribute(): ?string
    {
        return UploadHelper::url(
            $this->profile_image
        );
    }

    /**
     * User belongs to role
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Student profile (only for student users)
     */
    public function studentProfile(): HasOne
    {
        return $this->hasOne(StudentProfile::class);
    }

    public function teacherProfile()
    {
        return $this->hasOne(
            TeacherProfile::class
        );
    }

    /**
     * Exams created by admin/teacher
     */
    public function createdExams(): HasMany
    {
        return $this->hasMany(Exam::class, 'created_by');
    }

    /**
     * Questions created by admin/teacher
     */
    public function createdQuestions(): HasMany
    {
        return $this->hasMany(Question::class, 'created_by');
    }

    /**
     * Student exam attempts
     */
    public function examAttempts(): HasMany
    {
        return $this->hasMany(ExamAttempt::class, 'student_id');
    }

    /**
     * Student answers
     */
    public function studentAnswers(): HasMany
    {
        return $this->hasMany(StudentAnswer::class, 'student_id');
    }

    /**
     * Helper methods
     */
    public function isAdmin(): bool
    {
        return optional($this->role)->slug === 'admin';
    }

    public function isStudent(): bool
    {
        return optional($this->role)->slug === 'student';
    }

    /**
     * Direct user permissions
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'user_permissions')
            ->withTimestamps();
    }

    /**
     * Check if user is super admin
     */
    public function isSuperAdmin(): bool
    {
        return optional($this->role)->slug === 'super_admin';
    }

    /**
     * Check if user has a specific role slug
     */
    public function hasRole(string $roleSlug): bool
    {
        return optional($this->role)->slug === $roleSlug;
    }

    /**
     * Check if user has any role from given array
     */
    public function hasAnyRole(array $roles): bool
    {
        return in_array(optional($this->role)->slug, $roles, true);
    }

    /**
     * Check if user has a permission (Role-based with User-level customization support)
     */
    public function hasPermission(string $permissionSlug): bool
    {
        // Inactive accounts have no access
        if ((int) $this->status !== 1) {
            return false;
        }

        // If user has custom permissions enabled, check direct permissions (user-level override)
        if ($this->has_custom_permissions) {
            $this->loadMissing('permissions');

            return $this->permissions
                ->where('status', 1)
                ->contains('slug', $permissionSlug);
        }

        // Super Admin role without custom restrictions bypasses all checks
        if ($this->isSuperAdmin()) {
            return true;
        }

        // Otherwise fallback to Role permissions
        $this->loadMissing('role.permissions');

        if (! $this->role || ! $this->role->status) {
            return false;
        }

        return $this->role->permissions
            ->where('status', 1)
            ->contains('slug', $permissionSlug);
    }

    /**
     * Alias helper, same as hasPermission
     */
    public function canAccess(string $permissionSlug): bool
    {
        return $this->hasPermission($permissionSlug);
    }

    /**
     * Get all effective permission slugs for the user
     */
    public function permissionSlugs(): array
    {
        if ($this->has_custom_permissions) {
            $this->loadMissing('permissions');

            return $this->permissions
                ->where('status', 1)
                ->pluck('slug')
                ->toArray();
        }

        if ($this->isSuperAdmin()) {
            return Permission::active()->pluck('slug')->toArray();
        }

        $this->loadMissing('role.permissions');

        if (! $this->role || ! $this->role->status) {
            return [];
        }

        return $this->role->permissions
            ->where('status', 1)
            ->pluck('slug')
            ->toArray();
    }

    /**
     * Get direct permission IDs assigned to this user
     */
    public function directPermissionIds(): array
    {
        $this->loadMissing('permissions');

        return $this->permissions->pluck('id')->toArray();
    }

    /**
     * Sync custom permissions for this user
     */
    public function syncCustomPermissions(array $permissionIds): void
    {
        $this->permissions()->sync($permissionIds);
        $this->update(['has_custom_permissions' => true]);
    }

    /**
     * Reset user permissions to inherit from their role
     */
    public function resetToRolePermissions(): void
    {
        $this->permissions()->detach();
        $this->update(['has_custom_permissions' => false]);
    }

    public function teacherSubjects()
    {
        return $this->hasMany(TeacherSubject::class);
    }
}
