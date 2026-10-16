<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Admin' }} | {{ $site->get('site_name') }}</title>
    @include('components.public.favicon')
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="{{ asset('assets/css/base.css') }}?v={{ @filemtime(public_path('assets/css/base.css')) }}" rel="stylesheet">
    <link href="{{ asset('assets/css/admin.css') }}?v={{ @filemtime(public_path('assets/css/admin.css')) }}" rel="stylesheet">
    @stack('head')
</head>
<body>
    <a class="skip-link" href="#main">Skip to content</a>
    @php
        $admin = auth()->user();
        $initials = collect(preg_split('/\s+/', trim($admin->name)))->filter()->take(2)->map(fn ($part) => strtoupper(substr($part, 0, 1)))->implode('');
        $catalog = [
            'categories' => ['Categories', 'grid'],
            'courses' => ['Courses', 'book'],
            'quizzes' => ['Quizzes', 'page'],
            'services' => ['Services', 'grid'],
        ];
        $contentNav = [
            'blogs' => ['Blogs', 'page'],
            'clients' => ['Clients', 'users'],
            'corporate-images' => ['Corporate images', 'image'],
            'testimonials' => ['Testimonials', 'users'],
            'trainers' => ['Trainers', 'users'],
            'hall-of-fame' => ['Hall of fame', 'users'],
            'video-stories' => ['Video stories', 'play'],
            'pages' => ['Pages', 'page'],
        ];
    @endphp
    <div class="admin-shell">
        <aside class="sidebar">
            <div class="side-brand">
                <a href="{{ $admin->isSuperAdmin() ? route('admin.dashboard') : route('admin.seo.dashboard') }}" aria-label="{{ $site->get('site_name') }}">
                    <img src="{{ asset($site->get('logo')) }}" alt="{{ $site->get('site_name') }}">
                </a>
                <button class="side-collapse" type="button" data-side-collapse aria-label="Collapse menu">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m14 6-6 6 6 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
            </div>
            <nav aria-label="Admin">
                @if ($admin->isSuperAdmin())
                    <a class="{{ request()->routeIs('admin.dashboard') ? 'is-active' : '' }}" href="{{ route('admin.dashboard') }}">
                        <span class="nav-icon">@include('components.admin.icon', ['name' => 'grid'])</span>
                        <span class="nav-label">Dashboard</span>
                    </a>
                    <p class="nav-group">Catalog</p>
                    @foreach ($catalog as $key => [$label, $icon])
                        <a class="{{ request()->route('module') === $key || ($key === 'courses' && request()->routeIs('admin.courses.*')) ? 'is-active' : '' }}" href="{{ route('admin.modules.index', $key) }}">
                            <span class="nav-icon">@include('components.admin.icon', ['name' => $icon])</span>
                            <span class="nav-label">{{ $label }}</span>
                        </a>
                    @endforeach
                    <a class="{{ request()->routeIs('admin.quiz-results.*') ? 'is-active' : '' }}" href="{{ route('admin.quiz-results.index') }}">
                        <span class="nav-icon">@include('components.admin.icon', ['name' => 'check'])</span>
                        <span class="nav-label">Quiz results</span>
                    </a>
                    <p class="nav-group">Content</p>
                    @foreach ($contentNav as $key => [$label, $icon])
                        <a class="{{ request()->route('module') === $key ? 'is-active' : '' }}" href="{{ route('admin.modules.index', $key) }}">
                            <span class="nav-icon">@include('components.admin.icon', ['name' => $icon])</span>
                            <span class="nav-label">{{ $label }}</span>
                        </a>
                    @endforeach
                    <p class="nav-group">Sales</p>
                    <a class="{{ request()->routeIs('admin.enquiries.*') ? 'is-active' : '' }}" href="{{ route('admin.enquiries.index') }}">
                        <span class="nav-icon">@include('components.admin.icon', ['name' => 'mail'])</span>
                        <span class="nav-label">Enquiries</span>
                    </a>
                    <a class="{{ request()->routeIs('admin.orders.*') ? 'is-active' : '' }}" href="{{ route('admin.orders.index') }}">
                        <span class="nav-icon">@include('components.admin.icon', ['name' => 'cart'])</span>
                        <span class="nav-label">Orders</span>
                    </a>
                    <p class="nav-group">Account</p>
                    <a class="{{ request()->routeIs('admin.users.*') ? 'is-active' : '' }}" href="{{ route('admin.users.index') }}">
                        <span class="nav-icon">@include('components.admin.icon', ['name' => 'users'])</span>
                        <span class="nav-label">Users</span>
                    </a>
                    <a class="{{ request()->routeIs('admin.settings.*') ? 'is-active' : '' }}" href="{{ route('admin.settings.edit') }}">
                        <span class="nav-icon">@include('components.admin.icon', ['name' => 'gear'])</span>
                        <span class="nav-label">Settings</span>
                    </a>
                @endif
                <a class="{{ request()->routeIs('admin.seo.*') ? 'is-active' : '' }}" href="{{ route('admin.seo.dashboard') }}">
                    <span class="nav-icon">@include('components.admin.icon', ['name' => 'search'])</span>
                    <span class="nav-label">SEO</span>
                </a>
            </nav>
        </aside>
        <button class="side-scrim" type="button" data-side-scrim aria-label="Close menu"></button>
        <div class="admin-main">
            <header class="admin-top">
                <button class="menu-btn" type="button" data-side-open aria-label="Open menu">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 7h16M4 12h16M4 17h16" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                </button>
                <details class="user-menu">
                    <summary aria-label="{{ $admin->name }}">{{ $initials }}</summary>
                    <div class="user-card">
                        <strong>{{ $admin->name }}</strong>
                        <span>{{ $admin->email }}</span>
                        <form method="post" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit">Log out</button>
                        </form>
                    </div>
                </details>
            </header>
            <main id="main">
                @include('components.flash')
                @yield('content')
            </main>
            <footer class="admin-foot">© {{ date('Y') }} {{ $site->get('site_name') }}. All rights reserved.</footer>
        </div>
    </div>
    <script src="{{ asset('assets/js/admin.js') }}?v={{ @filemtime(public_path('assets/js/admin.js')) }}" defer></script>
</body>
</html>
