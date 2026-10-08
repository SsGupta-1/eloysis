<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class PermissionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $user = Auth::guard('admin')->user() ?? $request->user();

        if (! $user) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Please login to continue.',
                ], 401);
            }

            return redirect()->route('admin.login')
                ->with('error', 'Please login to continue.');
        }

        // Resource-level smart action mapping (e.g. permission:resource:classes or permission:resource:staffs,admins)
        if (str_starts_with($permission, 'resource:')) {
            $resourceParts = explode(':', $permission, 2)[1];
            $resourceConfig = explode(',', $resourceParts);
            $moduleSlug = trim($resourceConfig[1] ?? $resourceConfig[0]);

            $actionMethod = $request->route()?->getActionMethod() ?? 'index';
            $method = strtoupper($request->method());

            if (in_array($actionMethod, ['index', 'list', 'show', 'search', 'getSuggestedRollNumber', 'byClass', 'students', 'history', 'getPermissions', 'files', 'read', 'download', 'structures', 'searchStudents', 'enrollments', 'admitCards', 'marksEntry', 'examResults', 'tabulationPrint', 'studentResults', 'checkDuplicates', 'convert', 'studentLedger', 'print'], true) && $method === 'GET') {
                $requiredPermission = "{$moduleSlug}.view";
            } elseif (in_array($actionMethod, ['create', 'store', 'bulk', 'promote', 'save', 'storeConversion', 'generateSets', 'autoGenerateBlueprint', 'assignStaff', 'addFollowup', 'collect'], true) || ($method === 'POST' && ! in_array($actionMethod, ['update', 'destroy', 'changeStatus', 'updateStatus', 'updatePermissions', 'clear', 'publishToggle', 'updateStudentEligibility', 'saveMarks', 'cancel', 'toggleLock', 'handleApproval']))) {
                $requiredPermission = "{$moduleSlug}.create";
            } elseif (in_array($actionMethod, ['edit', 'update', 'status', 'changeStatus', 'updateStatus', 'toggleStatus', 'toggleLock', 'handleApproval', 'updatePermissions', 'publishToggle', 'updateStudentEligibility', 'saveMarks'], true) || in_array($method, ['PUT', 'PATCH'], true)) {
                $requiredPermission = "{$moduleSlug}.edit";
            } elseif (in_array($actionMethod, ['destroy', 'delete', 'clear', 'cancel'], true) || $method === 'DELETE') {
                $requiredPermission = "{$moduleSlug}.delete";
            } else {
                $requiredPermission = "{$moduleSlug}.view";
            }

            if (! $user->hasPermission($requiredPermission)) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'status' => false,
                        'message' => 'You do not have permission to perform this action.',
                    ], 403);
                }

                abort(403, 'You do not have permission to access this page.');
            }

            return $next($request);
        }

        // Support multiple permissions separated by pipe '|' (OR logic)
        $permissions = explode('|', $permission);
        $hasPermission = false;

        foreach ($permissions as $perm) {
            if ($user->hasPermission(trim($perm))) {
                $hasPermission = true;
                break;
            }
        }

        if (! $hasPermission) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => false,
                    'message' => 'You do not have permission to perform this action.',
                ], 403);
            }

            abort(403, 'You do not have permission to access this page.');
        }

        return $next($request);
    }
}
