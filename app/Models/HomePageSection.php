<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HomePageSection extends Model
{
    use HasFactory;

    protected $fillable = [
        'section_key',
        'section_type',
        'title',
        'subtitle',
        'is_enabled',
        'display_order',
        'layout_key',
        'custom_class',
        'settings',
        'is_system',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
        'is_system' => 'boolean',
        'display_order' => 'integer',
        'settings' => 'array',
    ];

    /**
     * Bootstrap the model and its traits.
     */
    protected static function booted(): void
    {
        static::creating(function ($section) {
            if (empty($section->section_key)) {
                $section->section_key = ($section->section_type ?? 'section').'_'.uniqid();
            }
        });
    }

    /**
     * Scope for active/enabled sections ordered by display_order.
     */
    public function scopeEnabled($query)
    {
        return $query->where('is_enabled', true)->orderBy('display_order', 'asc');
    }

    /**
     * Scope ordered.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order', 'asc');
    }
}
