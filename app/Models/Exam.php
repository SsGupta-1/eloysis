<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Exam extends Model
{
    public const MODE_OFFLINE = 'offline';
    public const MODE_ONLINE = 'online';
    public const MODE_BOTH = 'both';

    public const TYPE_UNIT_TEST = 'unit_test';
    public const TYPE_MID_TERM = 'mid_term';
    public const TYPE_QUARTERLY = 'quarterly';
    public const TYPE_HALF_YEARLY = 'half_yearly';
    public const TYPE_ANNUAL = 'annual';
    public const TYPE_PRACTICAL = 'practical';
    public const TYPE_ENTRANCE = 'entrance';
    public const TYPE_MOCK = 'mock';
    public const TYPE_OTHER = 'other';

    public const STATUS_DRAFT = 'draft';
    public const STATUS_PUBLISHED = 'published';
    public const STATUS_CLOSED = 'closed';

    protected $fillable = [
        'academic_session_id',
        'class_id',
        'subject_id',
        'title',
        'exam_code',
        'exam_type',
        'exam_mode',
        'description',
        'instructions',
        'duration_minutes',
        'total_marks',
        'passing_marks',
        'total_questions',
        'start_at',
        'end_at',
        'start_date',
        'end_date',
        'max_attempts',
        'negative_marking',
        'negative_marks_per_wrong',
        'shuffle_questions',
        'shuffle_options',
        'show_result_immediately',
        'status',
        'is_published',
        'grading_scale_id',
        'created_by',
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'start_date' => 'date',
        'end_date' => 'date',
        'negative_marking' => 'boolean',
        'shuffle_questions' => 'boolean',
        'shuffle_options' => 'boolean',
        'show_result_immediately' => 'boolean',
        'is_published' => 'boolean',
        'total_marks' => 'decimal:2',
        'passing_marks' => 'decimal:2',
        'negative_marks_per_wrong' => 'decimal:2',
    ];

    public function academicSession(): BelongsTo
    {
        return $this->belongsTo(AcademicSession::class, 'academic_session_id');
    }

    public function academicClass(): BelongsTo
    {
        return $this->belongsTo(AcademicClass::class, 'class_id');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(ExamSchedule::class, 'exam_id')->orderBy('exam_date')->orderBy('start_time');
    }

    public function enrolledStudents(): HasMany
    {
        return $this->hasMany(ExamStudentEnrollment::class, 'exam_id');
    }

    public function questions(): BelongsToMany
    {
        return $this->belongsToMany(Question::class, 'exam_questions')
            ->withPivot(['id', 'sort_order', 'marks'])
            ->withTimestamps();
    }

    public function examQuestions(): HasMany
    {
        return $this->hasMany(ExamQuestion::class);
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(ExamAttempt::class);
    }

    public function studentAnswers(): HasMany
    {
        return $this->hasMany(StudentAnswer::class);
    }
}
