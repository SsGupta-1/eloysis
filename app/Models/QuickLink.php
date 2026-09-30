<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuickLink extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'icon',
        'url',
        'color',
        'sort_order',
        'status',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'status' => 'boolean',
    ];

    /**
     * Scope active quick links.
     */
    public function scopeActive($query)
    {
        return $query->where('status', true)->orderBy('sort_order', 'asc');
    }
}
