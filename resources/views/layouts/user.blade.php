<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'My learning') | {{ $site->get('site_name') }}</title>
    @include('components.public.favicon')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="{{ asset('assets/css/base.css') }}?v={{ @filemtime(public_path('assets/css/base.css')) }}" rel="stylesheet">
    <link href="{{ asset('assets/css/components.css') }}?v={{ @filemtime(public_path('assets/css/components.css')) }}" rel="stylesheet">
    <link href="{{ asset('assets/css/account.css') }}?v={{ @filemtime(public_path('assets/css/account.css')) }}" rel="stylesheet">
    @stack('head')
</head>
<body class="is-learn">
    <a class="skip-link" href="#main">Skip to content</a>
    @include('components.public.header')
    @include('components.public.cart-drawer')
    @include('components.toast')
    <div class="learn-app">
        <aside class="learn-side">
            <div class="learn-sidecard">
                <div class="learn-id">
                    <span class="learn-avatar" aria-hidden="true">@include('components.public.user-avatar', ['user' => auth()->user()])</span>
                    <strong>{{ auth()->user()->name }}</strong>
                    <span class="learn-role">Learner</span>
                </div>
                <nav class="learn-nav" aria-label="Account">
                    <p>Main</p>
                    <a class="{{ request()->routeIs('account.dashboard') ? 'is-active' : '' }}" href="{{ route('account.dashboard') }}">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 10.5 12 4l8 6.5V20a1 1 0 0 1-1 1h-5v-6H10v6H5a1 1 0 0 1-1-1v-9.5Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/></svg>
                        Dashboard
                    </a>
                    <a class="{{ request()->routeIs('account.profile.*') ? 'is-active' : '' }}" href="{{ route('account.profile.edit') }}">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="12" cy="8" r="3" stroke="currentColor" stroke-width="1.7"/><path d="M6 19c1.2-2.4 3.3-3.6 6-3.6s4.8 1.2 6 3.6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
                        My profile
                    </a>
                    <p>Learning</p>
                    @php($onServices = request()->routeIs('account.services.*', 'learn.services.*') || filled(request()->route('pdf')?->service_id))
                    <a class="{{ ! $onServices && request()->routeIs('account.courses.*', 'learn.*') ? 'is-active' : '' }}" href="{{ route('account.courses.index') }}">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M6 5h9a3 3 0 0 1 3 3v11H8a2 2 0 0 0-2 2V5Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/></svg>
                        My courses
                    </a>
                    <a class="{{ $onServices ? 'is-active' : '' }}" href="{{ route('account.services.index') }}">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true"><rect x="4" y="7" width="16" height="12" rx="2" stroke="currentColor" stroke-width="1.7"/><path d="M9 7V5.5A1.5 1.5 0 0 1 10.5 4h3A1.5 1.5 0 0 1 15 5.5V7M4 12h16" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/></svg>
                        My services
                    </a>
                    <a class="{{ request()->routeIs('account.quizzes.*', 'quizzes.attempt', 'quizzes.results') ? 'is-active' : '' }}" href="{{ route('account.quizzes.index') }}">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true"><rect x="5" y="3" width="14" height="18" rx="2" stroke="currentColor" stroke-width="1.7"/><path d="m8.5 12 2 2 4.5-5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        Quizzes
                    </a>
                    <a class="{{ request()->routeIs('account.orders.*') ? 'is-active' : '' }}" href="{{ route('account.orders.index') }}">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M7 7h11l-1.2 8.2a1.5 1.5 0 0 1-1.48 1.3H9.2a1.5 1.5 0 0 1-1.48-1.25L6.4 4.5H4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        My orders
                    </a>
                    <p>Account</p>
                    <form method="post" action="{{ route('logout') }}">
                        @csrf
                        <button class="is-logout" type="submit">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M10 7V5a2 2 0 0 1 2-2h7v18h-7a2 2 0 0 1-2-2v-2" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/><path d="M4 12h10M11 8l4 4-4 4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            Log out
                        </button>
                    </form>
                </nav>
            </div>
            @if ($continueEnrollment?->course)
                <a class="learn-resume" href="{{ route('learn.show', $continueEnrollment->course) }}">
                    <small>Continue learning</small>
                    <strong>{{ $continueEnrollment->course->title }}</strong>
                    <span>Pick up where you left off</span>
                    <em>Open course</em>
                </a>
            @elseif ($continueService?->service)
                <a class="learn-resume" href="{{ route('learn.services.show', $continueService->service) }}">
                    <small>Your service</small>
                    <strong>{{ $continueService->service->title }}</strong>
                    <span>Open your study material and videos</span>
                    <em>Open service</em>
                </a>
            @elseif ($waitingOrder)
                <a class="learn-resume" href="{{ route('account.orders.show', $waitingOrder) }}">
                    <small>Waiting for approval</small>
                    <strong>{{ $waitingOrder->number }}</strong>
                    <span>You can open the course after an administrator approves it.</span>
                    <em>View order</em>
                </a>
            @endif
        </aside>
        <div class="learn-main" id="main">
            @include('components.flash')
            @yield('content')
        </div>
    </div>
    @include('components.public.footer')
    <script src="{{ asset('assets/js/site.js') }}?v={{ @filemtime(public_path('assets/js/site.js')) }}" defer></script>
    @stack('scripts')
</body>
</html>
