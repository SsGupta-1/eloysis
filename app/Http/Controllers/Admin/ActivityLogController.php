<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use App\Services\Admin\ActivityLogService;
use Illuminate\Http\Request;

class ActivityLogController extends BaseController
{
    public function __construct(
        protected ActivityLogService $activityLogService
    ) {}

    /**
     * Display Activity Logs Audit Trail index page
     */
    public function index()
    {
        $filterOptions = $this->activityLogService->getFilterOptions();

        return view('admin.activity-logs.index', $filterOptions);
    }

    /**
     * AJAX DataTables listing
     */
    public function list(Request $request)
    {
        $filters = [
            'search' => $request->input('search.value'),
            'user_id' => $request->input('user_id'),
            'module' => $request->input('module'),
            'action' => $request->input('action'),
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
        ];

        $length = max((int) $request->input('length', 15), 1);
        $start = max((int) $request->input('start', 0), 0);
        $page = (int) floor($start / $length) + 1;

        $orderColumn = $request->input('order.0.column');
        $orderDirection = $request->input('order.0.dir', 'desc');

        $logs = $this->activityLogService->list(
            filters: $filters,
            perPage: $length,
            page: $page,
            orderColumn: $orderColumn !== null ? (int) $orderColumn : null,
            orderDirection: $orderDirection
        );

        return $this->datatable($logs, (int) $request->input('draw', 1));
    }

    /**
     * Show single log JSON payload in modal
     */
    public function show(int $id)
    {
        $log = $this->activityLogService->find($id);

        if (! $log) {
            return $this->error('Activity log record not found.', [], 404);
        }

        return $this->success('Activity log details retrieved successfully.', [
            'log' => $log,
        ]);
    }

    /**
     * Clean old activity logs (Super Admin action)
     */
    public function clear(Request $request)
    {
        $days = max((int) $request->input('days', 30), 7);
        $deletedCount = $this->activityLogService->clearOldLogs($days);

        return $this->success("Successfully cleared {$deletedCount} old activity logs older than {$days} days.");
    }
}
