<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class StudentAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::guard('student')->user();

        if (! $user) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Please log in to access the student portal.',
                ], 401);
            }

            return redirect()->route('student.login')
                ->with('error', 'Please log in to access the student portal.');
        }

        // Must have student role
        if (optional($user->role)->slug !== 'student') {
            Auth::guard('student')->logout();

            if ($request->expectsJson()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthorized. Only students can access this portal.',
                ], 403);
            }

            return redirect()->route('student.login')
                ->with('error', 'Unauthorized. Only students can access this portal.');
        }

        // Account must be active
        if ((int) $user->status !== 1) {
            Auth::guard('student')->logout();

            if ($request->expectsJson()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Your student account is currently inactive. Please contact administration.',
                ], 403);
            }

            return redirect()->route('student.login')
                ->with('error', 'Your student account is currently inactive. Please contact administration.');
        }

        return $next($request);
    }
}
