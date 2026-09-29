<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Question extends Model
{
    public const TYPE_MCQ = 'mcq';

    public const TYPE_TRUE_FALSE = 'true_false';

    public const TYPE_FILL_BLANKS = 'fill_blanks';

    public const TYPE_SHORT_ANSWER = 'short_answer';

    public const TYPE_LONG_ANSWER = 'long_answer';

    public const TYPE_DESCRIPTIVE = 'descriptive';

    public const TYPE_MATCH_FOLLOWING = 'match_following';

    public const DIFFICULTY_EASY = 'easy';

    public const DIFFICULTY_MEDIUM = 'medium';

    public const DIFFICULTY_HARD = 'hard';

    protected $fillable = [
        'class_id',
        'academic_session_id',
        'subject_id',
        'chapter_name',
        'topic_name',
        'question_type',
        'difficulty_level',
        'blooms_taxonomy',
        'question_text',
        'option_a',
        'option_b',
        'option_c',
        'option_d',
        'correct_option',
        'options_data',
        'correct_answer_data',
        'marks',
        'negative_marks',
        'explanation',
        'tags',
        'image_path',
        'attachment_url',
        'status',
        'created_by',
    ];

    protected $casts = [
        'options_data' => 'array',
        'correct_answer_data' => 'array',
        'tags' => 'array',
        'marks' => 'decimal:2',
        'negative_marks' => 'decimal:2',
        'status' => 'boolean',
    ];

    public function getOptionsAttribute(): array
    {
        if (! empty($this->options_data)) {
            return $this->options_data;
        }

        return array_filter([
            'a' => $this->option_a,
            'b' => $this->option_b,
            'c' => $this->option_c,
            'd' => $this->option_d,
        ]);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function academicClass(): BelongsTo
    {
        return $this->belongsTo(AcademicClass::class, 'class_id');
    }

    public function academicSession(): BelongsTo
    {
        return $this->belongsTo(AcademicSession::class, 'academic_session_id');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function exams(): BelongsToMany
    {
        return $this->belongsToMany(Exam::class, 'exam_questions')
            ->withPivot(['id', 'sort_order', 'marks'])
            ->withTimestamps();
    }

    public function examQuestions(): HasMany
    {
        return $this->hasMany(ExamQuestion::class);
    }

    public function paperItems(): HasMany
    {
        return $this->hasMany(QuestionPaperItem::class, 'question_id');
    }

    public function studentAnswers(): HasMany
    {
        return $this->hasMany(StudentAnswer::class);
    }
}
