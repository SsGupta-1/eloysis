<?php

namespace App\Services\Admin;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ActivityLogService
{
    /**
     * Get paginated activity logs with filters
     */
    public function list(
        array $filters = [],
        int $perPage = 15,
        int $page = 1,
        ?int $orderColumn = null,
        string $orderDirection = 'desc'
    ): LengthAwarePaginator {
        $query = ActivityLog::with('user:id,name,email,role_id')
            ->select('activity_logs.*');

        // Apply Search
        if (! empty($filters['search'])) {
            $query->search($filters['search']);
        }

        // Filter by User
        if (! empty($filters['user_id'])) {
            $query->filterUser((int) $filters['user_id']);
        }

        // Filter by Module
        if (! empty($filters['module'])) {
            $query->filterModule($filters['module']);
        }

        // Filter by Action
        if (! empty($filters['action'])) {
            $query->filterAction($filters['action']);
        }

        // Filter by Date Range
        if (! empty($filters['start_date']) || ! empty($filters['end_date'])) {
            $query->filterDateRange($filters['start_date'] ?? null, $filters['end_date'] ?? null);
        }

        // Sorting mapping
        $columns = [
            0 => 'id',
            1 => 'created_at',
            2 => 'user_name',
            3 => 'module',
            4 => 'action',
            5 => 'method',
            6 => 'ip',
            7 => 'status_code',
        ];

        $sortBy = $columns[$orderColumn] ?? 'created_at';
        $direction = in_array(strtolower($orderDirection), ['asc', 'desc'], true) ? $orderDirection : 'desc';

        return $query->orderBy($sortBy, $direction)
            ->paginate(perPage: $perPage, page: $page);
    }

    /**
     * Get distinct modules and actions for filter dropdowns
     */
    public function getFilterOptions(): array
    {
        $modules = ActivityLog::distinct()
            ->whereNotNull('module')
            ->orderBy('module')
            ->pluck('module')
            ->toArray();

        $actions = ActivityLog::distinct()
            ->whereNotNull('action')
            ->orderBy('action')
            ->pluck('action')
            ->toArray();

        $users = User::select('id', 'name', 'email')
            ->whereHas('role')
            ->orderBy('name')
            ->get();

        return [
            'modules' => $modules,
            'actions' => $actions,
            'users' => $users,
        ];
    }

    /**
     * Get single log details with formatted JSON
     */
    public function find(int $id): ?ActivityLog
    {
        return ActivityLog::with('user:id,name,email,role_id')->find($id);
    }

    /**
     * Delete logs older than specific days
     */
    public function clearOldLogs(int $days = 30): int
    {
        return ActivityLog::where('created_at', '<=', now()->subDays($days))->delete();
    }
}
