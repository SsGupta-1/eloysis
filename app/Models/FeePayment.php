<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class FeePayment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'receipt_no',
        'student_enrollment_id',
        'stu_profile_id',
        'academic_session_id',
        'payment_date',
        'subtotal_amount',
        'discount_amount',
        'fine_amount',
        'total_paid',
        'payment_mode',
        'transaction_reference',
        'bank_name',
        'cheque_date',
        'collected_by',
        'status',
        'remarks',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'cheque_date' => 'date',
        'subtotal_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'fine_amount' => 'decimal:2',
        'total_paid' => 'decimal:2',
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

    public function collector(): BelongsTo
    {
        return $this->belongsTo(User::class, 'collected_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(FeePaymentItem::class);
    }
}
