<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class ActivityLoggerMiddleware
{
    /**
     * Sensitive fields that must never be logged.
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
            $authUser = self::getAuthenticatedUser();

            $route = $request->route();

            $routeName = $route?->getName();

            $action = $route?->getActionName();

            $duration = round(
                (microtime(true) - $startTime) * 1000,
                2
            );

            $logData = [
                'user_id' => $authUser['id'] ?? null,
                'user_email' => $authUser['email'] ?? null,
                'guard' => $authUser['guard'] ?? null,
                'name' => $authUser['name'] ?? null,

                'method' => strtoupper($request->method()),

                'route' => $routeName,

                'action' => $action,

                'url' => $request->fullUrl(),

                'ip' => $request->ip(),

                'user_agent' => $request->userAgent(),

                'status' => $response->getStatusCode(),

                'duration_ms' => $duration,

                'input' => $this->sanitize(
                    $request->except($this->sensitiveFields)
                ),
            ];

            Log::info(
                'HTTP Request',
                $logData
            );

        } catch (\Throwable $e) {

            // Logging must NEVER break the actual application request.

            Log::error(
                'Activity Logger Failed',
                [
                    'message' => $e->getMessage(),
                    'url' => $request->fullUrl(),
                ]
            );
        }

        return $response;
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

    public function getAuthenticatedUser(): ?array
    {
        $guards = [
            'admin',
            'teacher',
            'student',
            'web',
        ];

        foreach ($guards as $guard) {

            if (Auth::guard($guard)->check()) {

                $user = Auth::guard($guard)->user();

                return [
                    'id' => $user->id ?? null,
                    'email' => $user->email ?? null,
                    'guard' => $guard,
                    'name' => $user->name ?? null,
                ];
            }
        }

        return null;
    }
}
