@extends('layouts.public')

@section('toast_errors', '1')

@push('head')
    <link href="{{ asset('assets/css/showcase.css') }}?v={{ @filemtime(public_path('assets/css/showcase.css')) }}" rel="stylesheet">
    <link href="{{ asset('assets/css/corporate.css') }}?v={{ @filemtime(public_path('assets/css/corporate.css')) }}" rel="stylesheet">
@endpush

@section('content')
    <section class="corp-hero has-world-map">
        <div class="wrap corp-hero-grid">
            <div>
                <h1>Build a certified leadership bench for your enterprise.</h1>
                <p class="lede">{{ $service->short_description }} Programs follow PMI standards and can be delivered online or in person.</p>
                <a class="btn btn-primary" href="#plan-cohort">Request a demo</a>
            </div>
            @php
                $reviewUrl = config('edhut.google_reviews_url');
                $mapCards = [
                    ['tl', 'Corporate Cohorts', $site->get('stat_corporate'), '<path d="M4 21V5l8-2v18M12 9h8v12M2.5 21h19M7.5 8h1M7.5 12h1M7.5 16h1M15.5 13h1M15.5 17h1" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>'],
                    ['tr', 'Professionals Trained', $site->get('stat_students'), '<circle cx="9" cy="8" r="3.2" stroke="currentColor" stroke-width="1.7"/><path d="M3.5 19c.9-3 3-4.6 5.5-4.6s4.6 1.6 5.5 4.6M15.5 5.2a3 3 0 0 1 0 5.6M17.5 14.6c1.6.6 2.6 2.1 3 4.4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>'],
                    ['bl', 'PMP Certified Alumni', $site->get('stat_pmp'), '<circle cx="12" cy="9" r="5" stroke="currentColor" stroke-width="1.7"/><path d="M8.5 13 7 21l5-2.5 5 2.5-1.5-8" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>'],
                    ['br', 'Google Rating', $site->get('rating').'/5', '<path d="m12 3.5 2.6 5.3 5.8.8-4.2 4.1 1 5.8-5.2-2.7-5.2 2.7 1-5.8-4.2-4.1 5.8-.8L12 3.5Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>'],
                ];
                $mapPoints = [
                    ['top', 'PMI Certifications', 'PMP · PgMP · PfMP · PMI-RMP', '<circle cx="12" cy="12" r="8.5" stroke="currentColor" stroke-width="1.7"/><circle cx="12" cy="12" r="4.5" stroke="currentColor" stroke-width="1.7"/><circle cx="12" cy="12" r="1.2" fill="currentColor"/>'],
                    ['right', 'Expert Trainers', 'PMI Authorized Trainers', '<circle cx="9" cy="8" r="3.2" stroke="currentColor" stroke-width="1.7"/><path d="M3.5 19c.9-3 3-4.6 5.5-4.6s4.6 1.6 5.5 4.6M15.5 5.2a3 3 0 0 1 0 5.6M17.5 14.6c1.6.6 2.6 2.1 3 4.4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>'],
                    ['bottom', 'Flexible Delivery', 'Online or in person', '<path d="M4 5.5c2.5-1 5.5-1 8 .5 2.5-1.5 5.5-1.5 8-.5V19c-2.5-1-5.5-1-8 .5-2.5-1.5-5.5-1.5-8-.5V5.5ZM12 6v13.5" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>'],
                    ['left', 'Cohort Design', 'Shaped to your organisation', '<circle cx="12" cy="12" r="8.5" stroke="currentColor" stroke-width="1.7"/><path d="M3.5 12h17M12 3.5c2.3 2.4 3.4 5.2 3.4 8.5s-1.1 6.1-3.4 8.5c-2.3-2.4-3.4-5.2-3.4-8.5S9.7 5.9 12 3.5Z" stroke="currentColor" stroke-width="1.7"/>'],
                ];
            @endphp
            <div class="corp-map">
                <div class="corp-map-hub">
                    <span class="corp-map-cross" aria-hidden="true"></span>
                    <div class="corp-map-core">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 3l7 3v5c0 4.5-3 8.4-7 10-4-1.6-7-5.5-7-10V6l7-3z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/><path d="M9 12l2 2 4-4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        <strong>PMI<br>ATP</strong>
                        <span>Authorized Training Partner</span>
                    </div>
                    @foreach ($mapPoints as [$pointSide, $pointTitle, $pointText, $pointIcon])
                        <div class="corp-map-point corp-map-{{ $pointSide }}">
                            <span class="corp-map-node" aria-hidden="true"><svg width="22" height="22" viewBox="0 0 24 24" fill="none">{!! $pointIcon !!}</svg></span>
                            <p class="corp-map-caption"><b>{{ $pointTitle }}</b><span>{{ $pointText }}</span></p>
                        </div>
                    @endforeach
                </div>
                @foreach ($mapCards as [$cardSide, $cardLabel, $cardValue, $cardIcon])
                    <article class="corp-map-card corp-map-card-{{ $cardSide }}">
                        <div class="corp-map-card-head">
                            <span class="corp-map-card-icon" aria-hidden="true"><svg width="18" height="18" viewBox="0 0 24 24" fill="none">{!! $cardIcon !!}</svg></span>
                            @if ($cardSide === 'br')
                                <a href="{{ $reviewUrl }}" target="_blank" rel="noopener noreferrer">{{ $cardLabel }}</a>
                            @else
                                <span>{{ $cardLabel }}</span>
                            @endif
                        </div>
                        <strong>{{ $cardValue }}</strong>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="home-clients band-plain">
        <div class="wrap">
            <p class="kicker">Teams that train with EdHut</p>
            @include('components.public.client-logos')
        </div>
    </section>

    <section class="section band-tint">
        <div class="wrap">
            <p class="kicker">Gallery</p>
            <h2>Enterprise training in action.</h2>
            @include('components.public.training-gallery')
        </div>
    </section>

    @php
        $programs = \App\Support\Content::get('corporate.programs');
        $why = \App\Support\Content::get('corporate.why');
    @endphp
    <section class="section band-plain corp-why">
        <div class="wrap">
            <p class="kicker">{{ $programs['kicker'] }}</p>
            <h2>{{ $programs['title'] }}</h2>
            <p class="lede">{{ $programs['lede'] }}</p>
            <div class="corp-why-grid">
                @foreach ($programs['items'] as $program)
                    @php $programCourse = isset($program['course']) ? $courses->firstWhere('slug', $program['course']) : null; @endphp
                    @if ($programCourse)
                        <a class="corp-why-card" href="{{ route('courses.show', $programCourse) }}">
                    @else
                        <article class="corp-why-card">
                    @endif
                        <span class="corp-why-icon" aria-hidden="true"><svg width="18" height="18" viewBox="0 0 24 24" fill="none">@include('components.public.icon', ['name' => $program['icon'] ?? ''])</svg></span>
                        <h3>{{ $program['title'] }}</h3>
                        <p>{{ $program['text'] }}</p>
                    @if ($programCourse)
                        </a>
                    @else
                        </article>
                    @endif
                @endforeach
            </div>
        </div>
    </section>

    <section class="section band-tint corp-why">
        <div class="wrap">
            <p class="kicker">{{ $why['kicker'] }}</p>
            <h2>{{ $why['title'] }}</h2>
            <p class="lede">{{ $why['lede'] }}</p>
            <div class="corp-why-grid">
                @foreach ($why['items'] as $whyItem)
                    <article class="corp-why-card">
                        <span class="corp-why-icon" aria-hidden="true"><svg width="18" height="18" viewBox="0 0 24 24" fill="none">@include('components.public.icon', ['name' => $whyItem['icon'] ?? ''])</svg></span>
                        <h3>{{ $whyItem['title'] }}</h3>
                        <p>{{ $whyItem['text'] }}</p>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    @php
        $plan = \App\Support\Content::get('corporate.plan');
        $planInterests = $courses->toBase()->map->shortLabel()->push($plan['interest_other'])->unique()->values();
    @endphp
    <section class="section band-plain corp-plan has-world-map" id="plan-cohort">
        <div class="wrap corp-plan-grid">
            <div class="corp-plan-copy">
                <p class="kicker">{{ $plan['kicker'] }}</p>
                <h2>{{ $plan['title'] }}</h2>
                <p>{{ $plan['lede'] }}</p>
            </div>
            <form class="corp-plan-card" method="post" action="{{ route('corporate.enquiry') }}">
                @csrf
                <p class="corp-plan-tag">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 3l7 3v5.5c0 4.2-2.9 7.9-7 9.5-4.1-1.6-7-5.3-7-9.5V6l7-3z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><path d="M9 12l2 2 4-4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    {{ $plan['form_kicker'] }}
                </p>
                <h3>{{ $plan['form_title'] }}</h3>
                <p class="corp-plan-sub">{{ $plan['form_lede'] }}</p>
                <div class="corp-plan-fields">
                    <label class="corp-field">
                        <span>Contact name <i>*</i></span>
                        <input class="input" name="name" required maxlength="120" autocomplete="name" value="{{ old('name') }}" @error('name') aria-invalid="true" @enderror>
                    </label>
                    <label class="corp-field">
                        <span>Work email <i>*</i></span>
                        <input class="input" type="email" name="email" required maxlength="255" autocomplete="email" value="{{ old('email') }}" @error('email') aria-invalid="true" @enderror>
                    </label>
                    <label class="corp-field">
                        <span>Phone</span>
                        <input class="input" type="tel" name="phone" inputmode="numeric" pattern="[0-9]{7,15}" maxlength="15" title="Digits only, 7 to 15 digits" autocomplete="tel" data-digits value="{{ old('phone') }}" @error('phone') aria-invalid="true" @enderror>
                    </label>
                    <label class="corp-field">
                        <span>Company <i>*</i></span>
                        <input class="input" name="company" required maxlength="160" autocomplete="organization" value="{{ old('company') }}" @error('company') aria-invalid="true" @enderror>
                    </label>
                    <label class="corp-field">
                        <span>Designation</span>
                        <input class="input" name="designation" maxlength="120" autocomplete="organization-title" placeholder="e.g. Head of PMO" value="{{ old('designation') }}" @error('designation') aria-invalid="true" @enderror>
                    </label>
                    <label class="corp-field">
                        <span>Team size</span>
                        <select class="select" name="team_size" @error('team_size') aria-invalid="true" @enderror>
                            <option value="">Select range</option>
                            @foreach ($plan['team_sizes'] as $teamSize)
                                <option value="{{ $teamSize }}" @selected(old('team_size') === $teamSize)>{{ $teamSize }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label class="corp-field">
                        <span>Area of interest</span>
                        <select class="select" name="interest" @error('interest') aria-invalid="true" @enderror>
                            <option value="">Select</option>
                            @foreach ($planInterests as $planInterest)
                                <option value="{{ $planInterest }}" @selected(old('interest') === $planInterest)>{{ $planInterest }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label class="corp-field">
                        <span>Preferred delivery</span>
                        <select class="select" name="delivery" @error('delivery') aria-invalid="true" @enderror>
                            <option value="">Select</option>
                            @foreach ($plan['deliveries'] as $planDelivery)
                                <option value="{{ $planDelivery }}" @selected(old('delivery') === $planDelivery)>{{ $planDelivery }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label class="corp-field corp-field-wide">
                        <span>Briefly describe your context and goals</span>
                        <textarea class="textarea" name="message" maxlength="5000" rows="4" @error('message') aria-invalid="true" @enderror>{{ old('message') }}</textarea>
                    </label>
                </div>
                <div class="corp-plan-foot">
                    <p>
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 3l7 3v5.5c0 4.2-2.9 7.9-7 9.5-4.1-1.6-7-5.3-7-9.5V6l7-3z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/></svg>
                        {{ $plan['form_foot'] }}
                    </p>
                    <button class="btn btn-primary" type="submit">
                        {{ $plan['submit'] }}
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>
                </div>
            </form>
        </div>
    </section>

    @php $who = \App\Support\Content::get('corporate.who'); @endphp
    <section class="section band-tint corp-who">
        <div class="wrap corp-who-grid">
            <div class="corp-who-head">
                <p class="kicker">{{ $who['kicker'] }}</p>
                <h2>{{ $who['title'] }}</h2>
                <p>{{ $service->short_description }}</p>
            </div>
            <ul class="corp-who-list">
                @foreach ($who['items'] as $whoItem)
                    <li>
                        <span class="corp-who-icon" aria-hidden="true"><svg width="16" height="16" viewBox="0 0 24 24" fill="none">@include('components.public.icon', ['name' => 'person-check'])</svg></span>
                        {{ $whoItem }}
                    </li>
                @endforeach
            </ul>
        </div>
    </section>

    <section class="section band-plain">
        <div class="wrap faq-wrap">
            <h2>Questions buyers usually ask.</h2>
            <div class="faq">
                <details>
                    <summary>Can the program be delivered online or in person?</summary>
                    <p>Yes. EdHut delivers corporate cohorts online and in person, with the format agreed when the quotation is prepared.</p>
                </details>
                <details>
                    <summary>Who teaches the cohort?</summary>
                    <p>PMI Authorized Trainers. Classroom schedules name the trainer for that batch.</p>
                </details>
                <details>
                    <summary>Which credentials can a team prepare for?</summary>
                    <p>PMP, PgMP, PfMP, and PMI-RMP. The curriculum follows the published standard for the credential you choose.</p>
                </details>
                <details>
                    <summary>What happens if EdHut cancels the event?</summary>
                    <p>If EdHut cancels an event, the published classroom policy pays a 100% refund to the delegate. Delegate cancellations follow the timing rules on the terms page.</p>
                </details>
                <details>
                    <summary>How do we get a price?</summary>
                    <p>Corporate tuition is quoted for the team. Use the form on this page, email {{ $site->get('email') }}, or call {{ $site->get('phone_display') }}.</p>
                </details>
            </div>
        </div>
    </section>

    @include('components.public.cta-band')
@endsection
