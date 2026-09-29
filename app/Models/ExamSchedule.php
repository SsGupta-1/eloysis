<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExamSchedule extends Model
{
    public const STATUS_SCHEDULED = 'scheduled';

    public const STATUS_IN_PROGRESS = 'in_progress';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'exam_id',
        'class_id',
        'section_id',
        'subject_id',
        'question_paper_id',
        'exam_date',
        'start_time',
        'end_time',
        'duration_minutes',
        'room_no',
        'invigilator_id',
        'max_theory_marks',
        'max_practical_marks',
        'max_internal_marks',
        'max_viva_marks',
        'total_marks',
        'passing_marks',
        'status',
        'created_by',
    ];

    protected $casts = [
        'exam_date' => 'date',
        'duration_minutes' => 'integer',
        'max_theory_marks' => 'decimal:2',
        'max_practical_marks' => 'decimal:2',
        'max_internal_marks' => 'decimal:2',
        'max_viva_marks' => 'decimal:2',
        'total_marks' => 'decimal:2',
        'passing_marks' => 'decimal:2',
    ];

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    public function academicClass(): BelongsTo
    {
        return $this->belongsTo(AcademicClass::class, 'class_id');
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function questionPaper(): BelongsTo
    {
        return $this->belongsTo(QuestionPaper::class);
    }

    public function invigilator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invigilator_id');
    }

    public function marks(): HasMany
    {
        return $this->hasMany(ExamMark::class, 'exam_schedule_id');
    }
}
