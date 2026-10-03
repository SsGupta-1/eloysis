<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentEnrollment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'stu_profile_id',
        'roll_number',
        'academic_session_id',
        'class_id',
        'section_id',
        'fee_discount_id',
        'status',
        'promoted_by',
    ];

    public function feeDiscount()
    {
        return $this->belongsTo(FeeDiscount::class, 'fee_discount_id');
    }

    public function student()
    {
        return $this->belongsTo(
            StudentProfile::class,
            'stu_profile_id'
        );
    }

    public function academicSession()
    {
        return $this->belongsTo(
            AcademicSession::class
        );
    }

    public function studentClass()
    {
        return $this->belongsTo(
            AcademicClass::class,
            'class_id'
        );
    }

    public function section()
    {
        return $this->belongsTo(
            Section::class
        );
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(
            StudentAttendance::class,
            'student_enrollment_id'
        );
    }

    public function feeAllocations(): HasMany
    {
        return $this->hasMany(
            StudentFeeAllocation::class,
            'student_enrollment_id'
        );
    }

    public function feePayments(): HasMany
    {
        return $this->hasMany(
            FeePayment::class,
            'student_enrollment_id'
        );
    }
}
