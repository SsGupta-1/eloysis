<?php

namespace App\Http\Middleware;

use App\Models\ActivityLog;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class ActivityLoggerMiddleware
{
    /**
     * Sensitive fields that must never be logged in cleartext.
     */
    protected array $sensitiveFields = [
        'password',
        'password_confirmation',
        'current_password',
        'new_password',
        'token',
        'api_token',
        'access_token',
        'refresh_token',
        'authorization',
        'cookie',
        'secret',
        'client_secret',
        'otp',
        'otp_code',
        '_token',
    ];

    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $startTime = microtime(true);

        $response = $next($request);

        try {
            $authUser = $this->getAuthenticatedUser($request);
            $route = $request->route();
            $routeName = $route?->getName();
            $action = $route?->getActionName();
            $method = strtoupper($request->method());
            $statusCode = $response->getStatusCode();

            $duration = round(
                (microtime(true) - $startTime) * 1000,
                2
            );

            $sanitizedInput = $this->sanitize(
                $request->all()
            );

            $logData = [
                'user_id' => $authUser['id'] ?? null,
                'user_name' => $authUser['name'] ?? null,
                'user_email' => $authUser['email'] ?? null,
                'guard' => $authUser['guard'] ?? null,
                'method' => $method,
                'route' => $routeName,
                'action' => $action,
                'url' => $request->fullUrl(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'status' => $statusCode,
                'duration_ms' => $duration,
                'input' => $sanitizedInput,
            ];

            // 1. File Logger: Log all HTTP requests to daily activity log channel
            Log::channel('activity')->info('HTTP Request', $logData);

            // 2. Database Audit Logger: Save ONLY state-changing actions (POST/PUT/PATCH/DELETE/LOGIN/LOGOUT)
            if ($this->shouldLogToDatabase($method, $routeName, $statusCode)) {
                $meta = $this->deriveModuleAndAction($routeName, $method, $request);

                ActivityLog::create([
                    'user_id' => $authUser['id'] ?? null,
                    'user_name' => $authUser['name'] ?? 'System',
                    'user_email' => $authUser['email'] ?? null,
                    'guard' => $authUser['guard'] ?? 'admin',
                    'module' => $meta['module'],
                    'action' => $meta['action'],
                    'description' => $meta['description'],
                    'method' => $method,
                    'route_name' => $routeName,
                    'url' => $request->fullUrl(),
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'status_code' => $statusCode,
                    'duration_ms' => $duration,
                    'payload' => ! empty($sanitizedInput) ? $sanitizedInput : null,
                ]);
            }
        } catch (\Throwable $e) {
            // Logging must NEVER break the actual application request.
            Log::error('Activity Logger Failed', [
                'message' => $e->getMessage(),
                'url' => $request->fullUrl(),
            ]);
        }

        return $response;
    }

    /**
     * Determine if this request should be recorded in database audit table
     */
    protected function shouldLogToDatabase(string $method, ?string $routeName, int $statusCode): bool
    {
        if ($statusCode >= 500) {
            return false;
        }

        // Always log mutating requests
        if (in_array($method, ['POST', 'PUT', 'PATCH', 'DELETE'], true)) {
            return true;
        }

        // Also log GET logout if triggered via GET
        if ($routeName === 'admin.logout') {
            return true;
        }

        return false;
    }

    /**
     * Derive human-readable module, action, and description from route metadata
     */
    protected function deriveModuleAndAction(?string $routeName, string $method, Request $request): array
    {
        if (empty($routeName)) {
            return [
                'module' => 'general',
                'action' => $method,
                'description' => "Executed {$method} on ".$request->path(),
            ];
        }

        $parts = explode('.', $routeName);
        if ($parts[0] === 'admin') {
            array_shift($parts);
        }

        $moduleRaw = $parts[0] ?? 'system';
        $subAction = end($parts);

        // Normalize module name
        $module = strtolower(str_replace(['-', '_'], ' ', $moduleRaw));

        $action = 'ACTION';
        $desc = "{$method} request on {$module}";

        if (Str::contains($routeName, 'permissions.update')) {
            $action = 'UPDATE_PERMISSIONS';
            $desc = "Updated permissions for {$module}";
        } elseif (Str::contains($routeName, 'status')) {
            $action = 'STATUS_CHANGE';
            $desc = "Changed status of {$module} item";
        } elseif ($subAction === 'store' || $method === 'POST') {
            if ($routeName === 'admin.login.submit' || $routeName === 'admin.login') {
                $module = 'auth';
                $action = 'LOGIN';
                $desc = 'User logged in successfully';
            } elseif ($routeName === 'admin.logout') {
                $module = 'auth';
                $action = 'LOGOUT';
                $desc = 'User logged out';
            } else {
                $action = 'CREATE';
                $desc = 'Created new '.Str::singular($module);
            }
        } elseif ($subAction === 'update' || in_array($method, ['PUT', 'PATCH'], true)) {
            $action = 'UPDATE';
            $desc = 'Updated '.Str::singular($module).' record';
        } elseif ($subAction === 'destroy' || $method === 'DELETE') {
            $action = 'DELETE';
            $desc = 'Deleted '.Str::singular($module).' record';
        }

        return [
            'module' => Str::slug($module, '_'),
            'action' => $action,
            'description' => ucfirst($desc),
        ];
    }

    /**
     * Recursively sanitize request data.
     */
    protected function sanitize(mixed $data): mixed
    {
        if (! is_array($data)) {
            return $data;
        }

        foreach ($data as $key => $value) {
            if (
                in_array(
                    strtolower((string) $key),
                    $this->sensitiveFields,
                    true
                )
            ) {
                $data[$key] = '********';

                continue;
            }

            if (is_array($value)) {
                $data[$key] = $this->sanitize($value);
            }
        }

        return $data;
    }

    /**
     * Get authenticated user info across dynamically configured guards
     */
    public function getAuthenticatedUser(?Request $request = null): ?array
    {
        $configuredGuards = array_keys(config('auth.guards', ['admin' => [], 'web' => []]));

        foreach ($configuredGuards as $guard) {
            try {
                if (Auth::guard($guard)->check()) {
                    $user = Auth::guard($guard)->user();

                    return [
                        'id' => $user->id ?? null,
                        'email' => $user->email ?? null,
                        'guard' => $guard,
                        'name' => $user->name ?? null,
                        'role' => $user->role->role_name ?? null,
                    ];
                }
            } catch (\Throwable) {
                // Ignore any undefined or misconfigured guard
                continue;
            }
        }

        // Fallback to request user
        if ($request && $request->user()) {
            $user = $request->user();

            return [
                'id' => $user->id ?? null,
                'email' => $user->email ?? null,
                'guard' => 'web',
                'name' => $user->name ?? null,
                'role' => $user->role->role_name ?? null,
            ];
        }

        return null;
    }
}
