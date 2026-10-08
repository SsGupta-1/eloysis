<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    use HasFactory, Prunable;

    public const UPDATED_AT = null;

    protected $fillable = [
        'user_id',
        'user_name',
        'user_email',
        'guard',
        'module',
        'action',
        'description',
        'method',
        'route_name',
        'url',
        'ip',
        'user_agent',
        'status_code',
        'duration_ms',
        'payload',
        'created_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'payload' => 'array',
            'status_code' => 'integer',
            'duration_ms' => 'float',
            'created_at' => 'datetime',
        ];
    }

    /**
     * User who performed the activity
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get prunable query for logs older than 60 days
     */
    public function prunable(): Builder
    {
        return static::where('created_at', '<=', now()->subDays(60));
    }

    /**
     * Scope filter by user ID
     */
    public function scopeFilterUser(Builder $query, ?int $userId): Builder
    {
        if (! empty($userId)) {
            $query->where('user_id', $userId);
        }

        return $query;
    }

    /**
     * Scope filter by module
     */
    public function scopeFilterModule(Builder $query, ?string $module): Builder
    {
        if (! empty($module)) {
            $query->where('module', $module);
        }

        return $query;
    }

    /**
     * Scope filter by action
     */
    public function scopeFilterAction(Builder $query, ?string $action): Builder
    {
        if (! empty($action)) {
            $query->where('action', $action);
        }

        return $query;
    }

    /**
     * Scope filter by date range
     */
    public function scopeFilterDateRange(Builder $query, ?string $startDate, ?string $endDate): Builder
    {
        if (! empty($startDate)) {
            $query->whereDate('created_at', '>=', $startDate);
        }

        if (! empty($endDate)) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        return $query;
    }

    /**
     * Scope search keyword across description, route, url, user_name, ip
     */
    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                    ->orWhere('user_name', 'like', "%{$search}%")
                    ->orWhere('user_email', 'like', "%{$search}%")
                    ->orWhere('ip', 'like', "%{$search}%")
                    ->orWhere('route_name', 'like', "%{$search}%")
                    ->orWhere('action', 'like', "%{$search}%")
                    ->orWhere('module', 'like', "%{$search}%");
            });
        }

        return $query;
    }
}
