@extends('layouts.public')

@push('head')
    <link href="{{ asset('assets/css/sections.css') }}?v={{ @filemtime(public_path('assets/css/sections.css')) }}" rel="stylesheet">
    <link href="{{ asset('assets/css/about.css') }}?v={{ @filemtime(public_path('assets/css/about.css')) }}" rel="stylesheet">
@endpush

@section('content')
    @php
        $band = 0;
        $tone = function () use (&$band) {
            return ++$band % 2 ? 'band-tint' : 'band-plain';
        };
        $principles = [
            ['shield', 'PMI standards', 'Instruction follows the published standard for the credential, whether that is PMP, PgMP, PfMP, or PMI-RMP.'],
            ['trainer', 'Practising trainers', 'Sessions use examples and case studies so the exam content stays tied to real project work.'],
            ['target', 'A path for the person', 'Advisory starts with a consultation on experience and goals, then recommends a certification path.'],
            ['clipboard-check', 'Support around the exam', 'Application writing, self-paced study, simulations, and PDU courses sit beside the live cohorts.'],
        ];
        $reasons = [
            'Globally recognised training that meets PMI standards',
            'Trainers with industry knowledge and certification expertise',
            'Programs shaped to an organisation’s needs, goals, and industry',
            'In-person classrooms and live online cohorts',
        ];
    @endphp
    <section class="about-hero">
        <div class="wrap">
            <div class="about-hero-copy">
                <p class="kicker">About us</p>
                <h1>A PMI Authorized Training Partner for project, program and portfolio professionals.</h1>
                <p>EdHut Solutions is an ISO 9001:2015 certified company in Okhla, New Delhi. PMI Authorized Training Partners deliver PMI-approved instruction so professionals can prepare for certification online and in person.</p>
                <p>Learners can prepare for PMP, PgMP, PfMP, and PMI-RMP with classroom cohorts, services around the application and the exam, and corporate programs shaped to a team.</p>
            </div>
        </div>
    </section>

    <section class="section about-stats {{ $tone() }}">
        <div class="wrap">
            @include('public.partials.stat-row')
        </div>
    </section>

    @include('public.partials.founder')

    <section class="section about-principles {{ $tone() }}">
        <div class="wrap">
            <div class="about-head">
                <p class="kicker">What we stand for</p>
                <h2>Our principles.</h2>
                <p>These principles shape how EdHut runs cohorts, supports candidates, and trains enterprise teams.</p>
            </div>
            <div class="principle-grid">
                @foreach ($principles as [$icon, $heading, $text])
                    <article class="principle-card">
                        <span class="principle-icon" aria-hidden="true"><svg width="18" height="18" viewBox="0 0 24 24" fill="none">@include('components.public.icon', ['name' => $icon])</svg></span>
                        <h3>{{ $heading }}</h3>
                        <p>{{ $text }}</p>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="section about-studio {{ $tone() }}">
        <div class="wrap studio-grid">
            <div class="studio-copy">
                <p class="kicker">The studio</p>
                <h2>Based in Okhla, New Delhi.</h2>
                <p>Visit the studio, or reach the EdHut team by email, phone, or WhatsApp.</p>
                <ul class="studio-contacts">
                    <li>
                        <span class="studio-icon" aria-hidden="true"><svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M12 21s-6.5-5.6-6.5-11a6.5 6.5 0 0 1 13 0c0 5.4-6.5 11-6.5 11Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/><circle cx="12" cy="10" r="2.4" stroke="currentColor" stroke-width="1.7"/></svg></span>
                        <span><small>Address</small>{{ $site->get('address') }}</span>
                    </li>
                    <li>
                        <span class="studio-icon" aria-hidden="true"><svg width="18" height="18" viewBox="0 0 24 24" fill="none">@include('components.public.icon', ['name' => 'mail'])</svg></span>
                        <span><small>Email</small><a href="mailto:{{ $site->get('email') }}">{{ $site->get('email') }}</a></span>
                    </li>
                    <li>
                        <span class="studio-icon" aria-hidden="true"><svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M8 4h3l1.2 3.2-1.8 1.1a12 12 0 0 0 5.3 5.3l1.1-1.8L20 13v3a2 2 0 0 1-2.2 2A16 16 0 0 1 4 6.2 2 2 0 0 1 6 4h2Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/></svg></span>
                        <span><small>Phone</small><a href="tel:{{ preg_replace('/[^\d+]/', '', (string) $site->get('phone')) }}">{{ $site->get('phone_display') }}</a></span>
                    </li>
                    <li>
                        <span class="studio-icon" aria-hidden="true"><svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M12 21a9 9 0 1 0-7.7-4.4L3 21l4.5-1.2A9 9 0 0 0 12 21Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/></svg></span>
                        <span><small>WhatsApp (preferred mode)</small><a href="https://wa.me/{{ $site->get('whatsapp') }}">{{ $site->get('phone_display') }}</a></span>
                    </li>
                </ul>
            </div>
            <aside class="studio-card">
                <p class="studio-badge">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" aria-hidden="true">@include('components.public.icon', ['name' => 'shield'])</svg>
                    {{ $site->get('iso_line') }}
                </p>
                <h3>Why professionals choose EdHut</h3>
                <ul>
                    @foreach ($reasons as $reason)
                        <li>
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.8"/><path d="m8.5 12.2 2.4 2.4 4.6-4.9" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            {{ $reason }}
                        </li>
                    @endforeach
                </ul>
                <p class="studio-rating">
                    <strong>{{ $site->get('rating') }}</strong>
                    <span><span class="stars" aria-hidden="true">★★★★★</span> {{ $site->get('stat_reviews') }} Google 5-star reviews, from {{ $site->get('review_count') }} reviews</span>
                </p>
            </aside>
        </div>
    </section>

    @include('components.public.cta-band', ['ctaTone' => $tone()])
@endsection
