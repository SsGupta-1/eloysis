<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AdmissionEnquiry extends Model
{
    protected $fillable = [
        'academic_session_id',
        'application_no',
        'student_name',
        'student_email',
        'student_phone',
        'alternate_phone',
        'date_of_birth',
        'gender',
        'parent_name',
        'parent_phone',
        'class_id',
        'source',
        'reference_type',
        'reference_id',
        'reference_name',
        'reference_phone',
        'message',
        'remarks',
        'status',
        'follow_up_date',
        'last_contacted_at',
        'next_follow_up_at',
        'handled_by',
        'assigned_to',
        'assigned_at',
        'assigned_by',
        'attempt_count',
        'last_attempt_at',
        'last_attempt_status',
        'converted_at',
        'converted_by',
        'student_profile_id',
        'enrollment_id',
        'converted_user_id',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'follow_up_date' => 'date',
        'last_contacted_at' => 'datetime',
        'next_follow_up_at' => 'datetime',
        'assigned_at' => 'datetime',
        'last_attempt_at' => 'datetime',
        'converted_at' => 'datetime',
        'attempt_count' => 'integer',
    ];

    public function academicSession(): BelongsTo
    {
        return $this->belongsTo(AcademicSession::class);
    }

    public function class(): BelongsTo
    {
        return $this->belongsTo(AcademicClass::class);
    }

    public function studentClass(): BelongsTo
    {
        return $this->belongsTo(AcademicClass::class, 'class_id');
    }

    public function handledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'handled_by');
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function convertedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'converted_by');
    }

    public function studentProfile(): BelongsTo
    {
        return $this->belongsTo(StudentProfile::class, 'student_profile_id');
    }

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(StudentEnrollment::class, 'enrollment_id');
    }

    public function convertedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'converted_user_id');
    }

    public function followups(): HasMany
    {
        return $this->hasMany(AdmissionEnquiryFollowup::class, 'admission_enquiry_id')->latest('created_at');
    }

    public function isConverted(): bool
    {
        return $this->status === 'converted' || ! empty($this->converted_at) || ! empty($this->student_profile_id);
    }

    public function scopeActive($query)
    {
        return $query->where('status', '!=', 'cancelled');
    }

    public function scopeForAcademicSession($query, $academicSessionId)
    {
        return $query->where('academic_session_id', $academicSessionId);
    }
}
