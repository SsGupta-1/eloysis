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
