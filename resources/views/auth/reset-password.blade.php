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
            <h2>Choose a new password</h2>
            <p class="login-sub">Set a new password for your EdHut account.</p>
            <form method="post" action="{{ route('password.store') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $request->route('token') }}">
                <label class="login-field">
                    <span>Email address</span>
                    <span class="input-shell">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 6h16v12H4V6Z" stroke="currentColor" stroke-width="1.6"/><path d="m4 7 8 6 8-6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        <input class="input" type="email" name="email" value="{{ old('email', $request->email) }}" placeholder="you@example.com" required autofocus>
                    </span>
                </label>
                <label class="login-field">
                    <span>Password</span>
                    <span class="input-shell">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true"><rect x="5" y="10" width="14" height="10" rx="2" stroke="currentColor" stroke-width="1.6"/><path d="M8 10V8a4 4 0 0 1 8 0v2" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
                        <input class="input" type="password" name="password" placeholder="••••••••" required data-password>
                        <button class="eye-btn" type="button" data-password-toggle aria-label="Show password">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12Z" stroke="currentColor" stroke-width="1.6"/><circle cx="12" cy="12" r="2.5" stroke="currentColor" stroke-width="1.6"/></svg>
                        </button>
                    </span>
                </label>
                <label class="login-field">
                    <span>Confirm password</span>
                    <span class="input-shell">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true"><rect x="5" y="10" width="14" height="10" rx="2" stroke="currentColor" stroke-width="1.6"/><path d="M8 10V8a4 4 0 0 1 8 0v2" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
                        <input class="input" type="password" name="password_confirmation" placeholder="••••••••" required>
                    </span>
                </label>
                <button class="btn btn-signin" type="submit">Update password</button>
            </form>
            <p class="login-foot">Back to <a href="{{ route('login') }}">Sign in</a></p>
        </div>
    </section>
@endsection
