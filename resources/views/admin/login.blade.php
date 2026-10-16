@extends('layouts.auth')

@section('content')
    <div class="staff-login">
        <h1>Sign in</h1>
        <form method="post" action="{{ route('admin.login.store') }}">
            @csrf
            <label class="login-field">
                <span>Email address</span>
                <span class="input-shell">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 6h16v12H4V6Z" stroke="currentColor" stroke-width="1.6"/><path d="m4 7 8 6 8-6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    <input class="input" type="email" name="email" value="{{ old('email') }}" placeholder="you@example.com" required autofocus>
                </span>
            </label>
            <label class="login-field">
                <span>Password</span>
                <span class="input-shell">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true"><rect x="5" y="10" width="14" height="10" rx="2" stroke="currentColor" stroke-width="1.6"/><path d="M8 10V8a4 4 0 0 1 8 0v2" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
                    <input class="input" type="password" name="password" placeholder="••••••••" required>
                </span>
            </label>
            <button class="btn btn-signin" type="submit">Sign in</button>
        </form>
        <p class="login-foot"><a href="{{ route('home') }}">Back to the website</a></p>
    </div>
@endsection
