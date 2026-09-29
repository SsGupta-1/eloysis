<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuestionPaperItem extends Model
{
    protected $fillable = [
        'question_paper_id',
        'section_id',
        'question_id',
        'set_code',
        'display_order',
        'marks',
    ];

    protected $casts = [
        'display_order' => 'integer',
        'marks' => 'decimal:2',
    ];

    public function questionPaper(): BelongsTo
    {
        return $this->belongsTo(QuestionPaper::class, 'question_paper_id');
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(QuestionPaperSection::class, 'section_id');
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class, 'question_id');
    }
}
