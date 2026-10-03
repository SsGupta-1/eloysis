<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StudentFeeAllocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_enrollment_id',
        'stu_profile_id',
        'academic_session_id',
        'fee_structure_id',
        'fee_head_id',
        'fee_discount_id',
        'title',
        'month',
        'year',
        'due_date',
        'amount',
        'discount_amount',
        'fine_amount',
        'paid_amount',
        'status',
    ];

    protected $casts = [
        'due_date' => 'date',
        'amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'fine_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
    ];

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(StudentEnrollment::class, 'student_enrollment_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(StudentProfile::class, 'stu_profile_id');
    }

    public function academicSession(): BelongsTo
    {
        return $this->belongsTo(AcademicSession::class);
    }

    public function feeStructure(): BelongsTo
    {
        return $this->belongsTo(FeeStructure::class);
    }

    public function feeHead(): BelongsTo
    {
        return $this->belongsTo(FeeHead::class);
    }

    public function feeDiscount(): BelongsTo
    {
        return $this->belongsTo(FeeDiscount::class);
    }

    public function paymentItems(): HasMany
    {
        return $this->hasMany(FeePaymentItem::class);
    }

    /**
     * Get the net payable amount after discount and fine.
     */
    public function getNetPayableAttribute(): float
    {
        return max(0, (float) $this->amount - (float) $this->discount_amount + (float) $this->fine_amount);
    }

    /**
     * Get remaining balance.
     */
    public function getRemainingBalanceAttribute(): float
    {
        return max(0, $this->net_payable - (float) $this->paid_amount);
    }
}
