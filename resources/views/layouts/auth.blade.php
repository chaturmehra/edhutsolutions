<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Account' }} | {{ $site->get('site_name') }}</title>
    @include('components.public.favicon')
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link href="{{ asset('assets/css/base.css') }}?v={{ @filemtime(public_path('assets/css/base.css')) }}" rel="stylesheet">
</head>
<body>
    <a class="skip-link" href="#main">Skip to content</a>
    <div class="auth-shell">
        <main id="main" class="auth-card">
            <a class="brand" href="{{ route('home') }}">
                <span class="brand-mark"><img src="{{ asset($site->get('logo')) }}" alt="{{ $site->get('site_name') }}"></span>
            </a>
            @include('components.flash')
            @yield('content')
        </main>
    </div>
    @include('components.public.footer')
    <script src="{{ asset('assets/js/admin.js') }}?v={{ @filemtime(public_path('assets/js/admin.js')) }}" defer></script>
</body>
</html>
