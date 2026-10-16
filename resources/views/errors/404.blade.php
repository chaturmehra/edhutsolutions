@php
    $notFound = \App\Support\Content::get('errors.not_found');
    $title = $notFound['meta_title'].' | '.config('app.name');
@endphp
@extends('layouts.public')

@push('head')
    <meta name="robots" content="noindex">
    <link href="{{ asset('assets/css/errors.css') }}?v={{ @filemtime(public_path('assets/css/errors.css')) }}" rel="stylesheet">
@endpush

@section('content')
    <section class="nf-hero has-world-map">
        <div class="wrap nf-inner">
            <div class="nf-code" aria-hidden="true">
                <span>4</span>
                <span class="nf-orbit">
                    <svg viewBox="0 0 120 120" fill="none">
                        <circle cx="60" cy="60" r="50" stroke="currentColor" stroke-width="10"/>
                        <circle class="nf-orbit-ring" cx="60" cy="60" r="34" stroke="currentColor" stroke-width="3" stroke-dasharray="6 9"/>
                        <path d="M60 36v24l15 10" stroke="currentColor" stroke-width="7" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </span>
                <span>4</span>
            </div>
            <p class="kicker">{{ $notFound['kicker'] }}</p>
            <h1>{{ $notFound['title'] }}</h1>
            <p class="lede">{{ $notFound['lede'] }}</p>
            <div class="hero-actions nf-actions">
                <a class="btn btn-primary" href="{{ route('home') }}">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M19 12H5M11 6l-6 6 6 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    {{ $notFound['home'] }}
                </a>
                <a class="btn btn-ghost" href="{{ route('contact') }}">{{ $notFound['contact'] }}</a>
            </div>
        </div>
    </section>

    <section class="section band-plain nf-links">
        <div class="wrap">
            <h2>{{ $notFound['links_title'] }}</h2>
            <div class="nf-grid">
                @foreach ($notFound['links'] as $link)
                    <a class="nf-card" href="{{ route($link['route'], $link['params'] ?? []) }}">
                        <span class="nf-card-icon" aria-hidden="true"><svg width="18" height="18" viewBox="0 0 24 24" fill="none">@include('components.public.icon', ['name' => $link['icon']])</svg></span>
                        <span class="nf-card-body">
                            <strong>{{ $link['title'] }}</strong>
                            <small>{{ $link['text'] }}</small>
                        </span>
                        <svg class="nf-card-arrow" width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </a>
                @endforeach
            </div>
        </div>
    </section>
@endsection
