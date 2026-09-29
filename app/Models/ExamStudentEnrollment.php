<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExamStudentEnrollment extends Model
{
    public const ELIGIBLE = 'eligible';

    public const DETAINED = 'detained';

    public const EXEMPTED = 'exempted';

    public const FEE_DEFAULTER = 'fee_defaulter';

    public const ATTENDANCE_PRESENT = 'present';

    public const ATTENDANCE_ABSENT = 'absent';

    public const ATTENDANCE_MEDICAL = 'medical_leave';

    protected $fillable = [
        'exam_id',
        'student_enrollment_id',
        'student_id',
        'eligibility_status',
        'admit_card_generated',
        'attendance_status',
        'remarks',
    ];

    protected $casts = [
        'admit_card_generated' => 'boolean',
    ];

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    public function studentEnrollment(): BelongsTo
    {
        return $this->belongsTo(StudentEnrollment::class);
    }

    public function studentProfile(): BelongsTo
    {
        return $this->belongsTo(StudentProfile::class, 'student_id');
    }
}
