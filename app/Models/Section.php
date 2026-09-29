<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Section extends Model
{
    protected $fillable = [
        'name',
        'code',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    /**
     * Section belongs to class
     */
    public function sectionClass(): BelongsTo
    {
        return $this->belongsTo(AcademicClass::class, 'class_id');
    }

    public function getSectionNameAttribute(): ?string
    {
        return $this->name;
    }
}
