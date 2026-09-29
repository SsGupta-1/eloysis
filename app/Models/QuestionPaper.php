<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuestionPaper extends Model
{
    use SoftDeletes;

    public const APPROVAL_DRAFT = 'draft';

    public const APPROVAL_PENDING = 'pending_approval';

    public const APPROVAL_APPROVED = 'approved';

    public const APPROVAL_REJECTED = 'rejected';

    public const STATUS_DRAFT = 'draft';

    public const STATUS_PUBLISHED = 'published';

    public const STATUS_ARCHIVED = 'archived';

    protected $fillable = [
        'academic_session_id',
        'class_id',
        'subject_id',
        'exam_id',
        'title',
        'paper_code',
        'total_marks',
        'duration_minutes',
        'instructions',
        'is_confidential',
        'is_locked',
        'approval_status',
        'approved_by',
        'approved_at',
        'version',
        'has_sets',
        'set_names',
        'shuffle_questions',
        'shuffle_options',
        'status',
        'created_by',
    ];

    protected $casts = [
        'total_marks' => 'decimal:2',
        'duration_minutes' => 'integer',
        'is_confidential' => 'boolean',
        'is_locked' => 'boolean',
        'has_sets' => 'boolean',
        'set_names' => 'array',
        'shuffle_questions' => 'boolean',
        'shuffle_options' => 'boolean',
        'approved_at' => 'datetime',
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

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function sections(): HasMany
    {
        return $this->hasMany(QuestionPaperSection::class, 'question_paper_id')->orderBy('sort_order');
    }

    public function items(): HasMany
    {
        return $this->hasMany(QuestionPaperItem::class, 'question_paper_id')->orderBy('display_order');
    }

    public function audits(): HasMany
    {
        return $this->hasMany(QuestionPaperAudit::class, 'question_paper_id')->latest('created_at');
    }

    public function isApproved(): bool
    {
        return $this->approval_status === self::APPROVAL_APPROVED;
    }

    public function isLocked(): bool
    {
        return (bool) $this->is_locked;
    }
}
