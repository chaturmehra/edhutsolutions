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
            <h2>Verify your email</h2>
            <p class="login-sub">Open the verification link we sent to <strong>{{ auth()->user()->email }}</strong>. You can continue after that link is opened.</p>
            <div class="verify-actions">
                <form method="post" action="{{ route('verification.send') }}">
                    @csrf
                    <button class="btn btn-signin" type="submit">Resend email</button>
                </form>
                <form method="post" action="{{ route('logout') }}">
                    @csrf
                    <button class="btn btn-ghost" type="submit">Log out</button>
                </form>
            </div>
        </div>
    </section>
@endsection
