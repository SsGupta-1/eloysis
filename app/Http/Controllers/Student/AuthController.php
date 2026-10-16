<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Show student login form
     */
    public function showLoginForm(): View
    {
        return view('student.auth.login');
    }

    /**
     * Handle student login submission
     */
    public function login(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [
            'login.required' => 'Please enter your Email, Admission No, or Mobile Number.',
            'password.required' => 'Please enter your password.',
        ]);

        $loginInput = trim($validated['login']);
        $password = $validated['password'];

        // Search user by email, or student profile admission_no, or mobile number
        $user = User::whereHas('role', function ($query) {
            $query->where('slug', 'student');
        })
            ->where(function ($query) use ($loginInput) {
                $query->where('email', $loginInput)
                    ->orWhere('mobile', $loginInput)
                    ->orWhereHas('studentProfile', function ($sq) use ($loginInput) {
                        $sq->where('admission_no', $loginInput);
                    });
            })
            ->first();

        if (! $user) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => false,
                    'message' => 'No student account found with the provided details.',
                ], 422);
            }

            return back()->withInput($request->only('login'))
                ->withErrors(['login' => 'No student account found with the provided details.']);
        }

        // Account status check
        if ((int) $user->status !== 1) {
            $inactiveMsg = 'Your student account is inactive. Please contact your school administration.';
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => false,
                    'message' => $inactiveMsg,
                ], 403);
            }

            return back()->withInput($request->only('login'))
                ->withErrors(['login' => $inactiveMsg]);
        }

        // Password check
        if (! Hash::check($password, $user->password)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => false,
                    'message' => 'The password you entered is incorrect.',
                ], 422);
            }

            return back()->withInput($request->only('login'))
                ->withErrors(['password' => 'The password you entered is incorrect.']);
        }

        // Perform login using 'student' guard
        Auth::guard('student')->login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        $user->update([
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'status' => true,
                'message' => 'Login successful! Redirecting to student portal...',
                'redirect' => route('student.dashboard'),
            ]);
        }

        return redirect()->intended(route('student.dashboard'))
            ->with('success', 'Welcome back, '.$user->name.'!');
    }

    /**
     * Log the student out
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('student')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('student.login')
            ->with('success', 'You have been logged out successfully.');
    }
}
