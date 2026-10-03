<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeePaymentItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'fee_payment_id',
        'student_fee_allocation_id',
        'amount_paid',
        'discount_applied',
        'fine_paid',
    ];

    protected $casts = [
        'amount_paid' => 'decimal:2',
        'discount_applied' => 'decimal:2',
        'fine_paid' => 'decimal:2',
    ];

    public function payment(): BelongsTo
    {
        return $this->belongsTo(FeePayment::class, 'fee_payment_id');
    }

    public function allocation(): BelongsTo
    {
        return $this->belongsTo(StudentFeeAllocation::class, 'student_fee_allocation_id');
    }
}
