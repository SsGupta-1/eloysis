<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentProfile extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'admission_date',
        'blood_group',
        'admission_no',
        'dob',
        'gender',
        'address',
        'father_name',
        'mother_name',
        'guardian_name',
        'guardian_mobile',
        'guardian_email',
        'city', 'state', 'pincode',
    ];

    protected $casts = [
        'dob' => 'date',
    ];

    /**
     * Profile belongs to user
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function enrollments()
    {
        return $this->hasMany(
            StudentEnrollment::class,
            'stu_profile_id'
        );
    }

    public function feeAllocations()
    {
        return $this->hasMany(
            StudentFeeAllocation::class,
            'stu_profile_id'
        );
    }

    public function feePayments()
    {
        return $this->hasMany(
            FeePayment::class,
            'stu_profile_id'
        );
    }
}
