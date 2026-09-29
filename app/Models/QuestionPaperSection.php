<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuestionPaperSection extends Model
{
    protected $fillable = [
        'question_paper_id',
        'section_name',
        'section_type',
        'total_questions',
        'marks_per_question',
        'instructions',
        'sort_order',
    ];

    protected $casts = [
        'total_questions' => 'integer',
        'marks_per_question' => 'decimal:2',
        'sort_order' => 'integer',
    ];

    public function questionPaper(): BelongsTo
    {
        return $this->belongsTo(QuestionPaper::class, 'question_paper_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(QuestionPaperItem::class, 'section_id')->orderBy('display_order');
    }
}
