@extends('layouts.auth.master')

@section('title', 'Admin Login')

@section('content')
<div class="auth-card" id="loginCard">

    {{-- Brand Section --}}
    <div class="auth-brand">
        <div class="auth-logo-wrapper">
            <img src="{{ asset('assets/common/images/logo.png') }}"
                 alt="{{ config('app.name', 'Eloysis') }}"
                 class="auth-logo">
        </div>
        <div>
            <span class="auth-badge">
                <span class="auth-badge-dot"></span>
                Admin Control Portal
            </span>
        </div>
        <h1 class="auth-title">Welcome Back</h1>
        <p class="auth-subtitle">Sign in to manage your system and dashboard</p>
    </div>

    {{-- Alert Banner for Instant Visual Feedback --}}
    <div id="loginAlert" class="auth-alert" role="alert"></div>

    {{-- Login Form --}}
    <form id="loginForm"
          action="{{ route('admin.login.submit') }}"
          method="POST"
          novalidate>
        @csrf

        {{-- Email / Username Input --}}
        <div class="form-group-custom">
            <label for="email" class="form-label-custom">
                Email or Username <span class="text-danger">*</span>
            </label>
            <div class="input-wrapper">
                <i class="bi bi-person-fill input-icon-left"></i>
                <input type="text"
                       id="email"
                       name="email"
                       class="form-control-custom"
                       placeholder="Enter your email or username"
                       autocomplete="username"
                       required
                       autofocus>
            </div>
        </div>

        {{-- Password Input with Eye Toggle --}}
        <div class="form-group-custom">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <label for="password" class="form-label-custom mb-0">
                    Password <span class="text-danger">*</span>
                </label>
            </div>
            <div class="input-wrapper">
                <i class="bi bi-shield-lock-fill input-icon-left"></i>
                <input type="password"
                       id="password"
                       name="password"
                       class="form-control-custom has-right-icon"
                       placeholder="Enter your password"
                       autocomplete="current-password"
                       required>
                <button type="button"
                        class="input-toggle-btn toggle-password-btn"
                        data-target="#password"
                        aria-label="Toggle password visibility"
                        title="Show / Hide Password">
                    <i class="bi bi-eye-slash"></i>
                </button>
            </div>
        </div>

        {{-- Options Row --}}
        <div class="auth-options">
            <label class="custom-checkbox">
                <input type="checkbox" name="remember" id="remember" value="1">
                <span>Remember me</span>
            </label>
            <a href="javascript:void(0)"
               class="auth-link"
               onclick="Swal.fire({
                   icon: 'info',
                   title: 'Password Reset',
                   text: 'Please contact your Super Administrator to reset your admin login credentials.',
                   confirmButtonColor: '#2563eb'
               });">
                Forgot password?
            </a>
        </div>

        {{-- Submit Button --}}
        <x-ui.button
            type="submit"
            id="loginSubmitBtn"
            class="btn-auth-submit w-100"
            icon="bi-box-arrow-in-right">
            Sign In to Account
        </x-ui.button>
    </form>

    {{-- Footer Security Note --}}
    <div class="auth-footer">
        <div class="security-badge">
            <i class="bi bi-shield-check"></i>
            <span>256-Bit Encrypted Secure Session</span>
        </div>
    </div>

</div>
@endsection