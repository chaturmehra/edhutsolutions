@extends('layouts.public')

@section('content')
    <section class="login-layout">
        <div class="login-copy">
            <p class="login-badge"><span class="dot" aria-hidden="true"></span> Authorized Training Partner</p>
            <h1>Elevate Your <span>Leadership Career</span></h1>
            <p>Access PMI-aligned mentoring, live cohorts, and study resources for project, program, and portfolio managers.</p>
            <ul class="login-points">
                <li>
                    <span class="point-icon point-icon-check" aria-hidden="true">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M20 6 9 17l-5-5" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </span>
                    <span><strong>Mentor-Led Certification Prep</strong> Instructed by PMI Authorized Trainers for PMP, PgMP, PfMP, and PMI-RMP.</span>
                </li>
                <li>
                    <span class="point-icon point-icon-check" aria-hidden="true">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M20 6 9 17l-5-5" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </span>
                    <span><strong>PMI Exam Alignment</strong> Preparation mapped to the published PMP, PgMP, PfMP, and PMI-RMP exam content.</span>
                </li>
                <li>
                    <span class="point-icon point-icon-shield" aria-hidden="true">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M12 3 5 6v6c0 4.2 2.8 7.4 7 9 4.2-1.6 7-4.8 7-9V6l-7-3Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/></svg>
                    </span>
                    <span><strong>Secure Dashboard Access</strong> Course material stays locked until an enrollment is approved.</span>
                </li>
            </ul>
        </div>

        <div class="login-card">
            @include('components.flash')
            <h2>Welcome back</h2>
            <p class="login-sub">Sign in to continue your learning journey.</p>
            <div class="social-row">
                <a class="social-btn" href="{{ route('google.redirect') }}">
                    <svg width="18" height="18" viewBox="0 0 24 24" aria-hidden="true"><path fill="#4285F4" d="M23 12.3c0-.8-.1-1.6-.2-2.3H12v4.4h6.2a5.3 5.3 0 0 1-2.3 3.5v2.9h3.7c2.2-2 3.4-5 3.4-8.5Z"/><path fill="#34A853" d="M12 24c3.2 0 5.9-1 7.9-2.9l-3.7-2.9c-1 .7-2.4 1.2-4.2 1.2-3.2 0-5.9-2.2-6.9-5.1H1.3v3A12 12 0 0 0 12 24Z"/><path fill="#FBBC05" d="M5.1 14.3A7.2 7.2 0 0 1 4.7 12c0-.8.1-1.6.4-2.3V6.7H1.3A12 12 0 0 0 0 12c0 1.9.5 3.8 1.3 5.3l3.8-3Z"/><path fill="#EA4335" d="M12 4.8c1.7 0 3.3.6 4.5 1.8l3.4-3.4C17.9 1.1 15.2 0 12 0A12 12 0 0 0 1.3 6.7l3.8 3C6.1 7 8.8 4.8 12 4.8Z"/></svg>
                    Continue with Google
                </a>
            </div>
            <p class="or-line"><span>Or</span></p>
            <form method="post" action="{{ route('login') }}">
                @csrf
                <label class="login-field">
                    <span>Email address</span>
                    <span class="input-shell">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 6h16v12H4V6Z" stroke="currentColor" stroke-width="1.6"/><path d="m4 7 8 6 8-6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        <input class="input" type="email" name="email" value="{{ old('email') }}" placeholder="you@example.com" required autofocus>
                    </span>
                </label>
                <label class="login-field">
                    <span class="label-row">Password <a href="{{ route('password.request') }}">Forgot password?</a></span>
                    <span class="input-shell">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true"><rect x="5" y="10" width="14" height="10" rx="2" stroke="currentColor" stroke-width="1.6"/><path d="M8 10V8a4 4 0 0 1 8 0v2" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
                        <input class="input" type="password" name="password" placeholder="••••••••" required data-password>
                        <button class="eye-btn" type="button" data-password-toggle aria-label="Show password">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12Z" stroke="currentColor" stroke-width="1.6"/><circle cx="12" cy="12" r="2.5" stroke="currentColor" stroke-width="1.6"/></svg>
                        </button>
                    </span>
                </label>
                <label class="remember">
                    <input type="checkbox" name="remember" value="1" @checked(old('remember'))>
                    <span>Remember me</span>
                </label>
                <button class="btn btn-signin" type="submit">Sign In <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg></button>
            </form>
            <p class="login-foot">Don't have an account? <a href="{{ route('register') }}">Sign up</a></p>
            <p class="login-foot">Want to enroll? <a href="{{ route('contact') }}">Request enrollment</a></p>
        </div>
    </section>
@endsection
