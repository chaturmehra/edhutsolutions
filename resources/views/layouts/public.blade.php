<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? $site->get('site_name') }}</title>
    <meta name="description" content="{{ $description ?? $site->get('footer_text') }}">
    @if (! empty($keywords))
        <meta name="keywords" content="{{ $keywords }}">
    @endif
    @include('components.public.favicon')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap-grid.min.css" rel="stylesheet">
    <link href="{{ asset('assets/css/base.css') }}?v={{ @filemtime(public_path('assets/css/base.css')) }}" rel="stylesheet">
    <link href="{{ asset('assets/css/components.css') }}?v={{ @filemtime(public_path('assets/css/components.css')) }}" rel="stylesheet">
    @if (request()->routeIs('home'))
        <link href="{{ asset('assets/css/showcase.css') }}?v={{ @filemtime(public_path('assets/css/showcase.css')) }}" rel="stylesheet">
        <link href="{{ asset('assets/css/sections.css') }}?v={{ @filemtime(public_path('assets/css/sections.css')) }}" rel="stylesheet">
        <link href="{{ asset('assets/css/home.css') }}?v={{ @filemtime(public_path('assets/css/home.css')) }}" rel="stylesheet">
    @endif
    @stack('head')
</head>
@php
    $guestAuth = request()->routeIs('login', 'register', 'password.request', 'password.reset', 'verification.notice');
    $toastErrors = \Illuminate\Support\Facades\View::hasSection('toast_errors');
@endphp
<body @class(['is-auth-page' => $guestAuth])>
    <a class="skip-link" href="#main">Skip to content</a>
    @include('components.public.header')
    @include('components.public.cart-drawer')
    @include('components.toast', ['withErrors' => $toastErrors])
    <main id="main">
        @unless ($guestAuth || $toastErrors)
            @include('components.flash')
        @endunless
        @yield('content')
    </main>
    @include('components.public.footer')
    @unless ($guestAuth)
        @include('components.public.notice')
        @include('components.public.lightbox')
    @endunless
    <script src="{{ asset('assets/js/site.js') }}?v={{ @filemtime(public_path('assets/js/site.js')) }}" defer></script>
</body>
</html>
