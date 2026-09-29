<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExamMark extends Model
{
    protected $fillable = [
        'exam_schedule_id',
        'student_enrollment_id',
        'theory_marks',
        'practical_marks',
        'internal_marks',
        'viva_marks',
        'total_marks',
        'is_absent',
        'is_exempted',
        'grade_point',
        'letter_grade',
        'remarks',
        'entered_by',
        'verified_by',
    ];

    protected $casts = [
        'theory_marks' => 'decimal:2',
        'practical_marks' => 'decimal:2',
        'internal_marks' => 'decimal:2',
        'viva_marks' => 'decimal:2',
        'total_marks' => 'decimal:2',
        'is_absent' => 'boolean',
        'is_exempted' => 'boolean',
        'grade_point' => 'decimal:2',
    ];

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(ExamSchedule::class, 'exam_schedule_id');
    }

    public function studentEnrollment(): BelongsTo
    {
        return $this->belongsTo(StudentEnrollment::class);
    }

    public function enteredByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'entered_by');
    }

    public function verifiedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
