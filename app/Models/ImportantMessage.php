<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImportantMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'message',
        'type',
        'action_text',
        'action_url',
        'start_date',
        'end_date',
        'status',
        'sort_order',
    ];

    protected $casts = [
        'status' => 'boolean',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'sort_order' => 'integer',
    ];

    /**
     * Scope active messages (status true and within valid date window if dates are set).
     */
    public function scopeActive($query)
    {
        $now = now();

        return $query->where('status', true)
            ->where(function ($q) use ($now) {
                $q->whereNull('start_date')->orWhere('start_date', '<=', $now);
            })
            ->where(function ($q) use ($now) {
                $q->whereNull('end_date')->orWhere('end_date', '>=', $now);
            })
            ->orderBy('sort_order', 'asc')
            ->orderByDesc('id');
    }
}
