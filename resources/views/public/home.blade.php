@extends('layouts.public')

@section('content')
    @php
        $reviewUrl = config('edhut.google_reviews_url');
        $band = 0;
        $tone = function () use (&$band) {
            return ++$band % 2 ? 'band-tint' : 'band-plain';
        };
        $tone();
    @endphp
    <section class="hero has-world-map">
        <div class="wrap hero-stage">
            <div class="hero-copy">
                <p class="badge"><img class="dot" src="{{ asset('assets/images/brand/pmi-atp-badge.png') }}" width="28" height="28" alt=""> PMI Authorized Training Partner</p>
                <h1><span class="line">Lead projects.</span><span class="line accent">Inspire change.</span><span class="line">Make an impact.</span></h1>
                <p class="lede">PMI-approved instruction, online and in person, so professionals can <em>prepare with confidence</em>.</p>
                <div class="hero-actions">
                    <a class="btn btn-primary" href="{{ route('contact') }}">Quick enquiry <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg></a>
                    <a class="btn btn-ghost" href="https://wa.me/{{ $site->get('whatsapp') }}">Chat with advisor</a>
                </div>
                <div class="rating-row">
                    <span class="avatar-stack">
                        <img class="avatar-strip" src="{{ asset('assets/images/brand/learner-avatars.jpg') }}" alt="EdHut learners">
                        <b>{{ $site->get('stat_students') }}</b>
                    </span>
                    <span class="rating-meta">
                        <span class="rating-score"><span class="stars" aria-hidden="true">★★★★★</span> <strong>{{ $site->get('rating') }}/5</strong></span>
                        <a href="{{ $reviewUrl }}" target="_blank" rel="noopener noreferrer">From {{ $site->get('review_count') }} reviews</a>
                    </span>
                </div>
            </div>
            <div class="hero-visual">
                <img class="hero-person" src="{{ asset('assets/images/brand/hero-mentor-cut.png') }}" alt="EdHut mentor">
                <aside class="float-card float-top">
                    <span class="float-badge" aria-hidden="true">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M12 3 5 6v6c0 4.2 2.8 7.2 7 9 4.2-1.8 7-4.8 7-9V6l-7-3Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/></svg>
                    </span>
                    <span><b>PMI ATP</b> Authorized instructor</span>
                </aside>
                <aside class="float-card float-bottom">
                    <span class="float-label">Learner rating</span>
                    <strong>{{ $site->get('rating') }}</strong>
                    <small><a href="{{ $reviewUrl }}" target="_blank" rel="noopener noreferrer">From {{ $site->get('review_count') }} reviews</a></small>
                </aside>
            </div>
            <div class="program-rail">
                <p><span>Explore our</span> PMI exam preparation programs</p>
                <div class="pill-row">
                    @foreach ($courses as $course)
                        <a class="pill" href="{{ route('courses.show', $course) }}">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="12" cy="8" r="3" stroke="currentColor" stroke-width="1.7"/><path d="M6 19c1.2-2.4 3.3-3.6 6-3.6s4.8 1.2 6 3.6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
                            {{ $course->shortLabel() }}
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <section class="home-clients {{ $tone() }}">
        <div class="wrap">
            <p class="kicker">Powering global teams</p>
            @include('components.public.client-logos')
            @include('public.partials.stat-row')
        </div>
    </section>

    <section class="section home-block {{ $tone() }}">
        <div class="wrap">
            <p class="kicker">Gallery</p>
            <h2>Enterprise training in action.</h2>
            @include('components.public.training-gallery')
        </div>
    </section>

    <section class="section {{ $tone() }}">
        <div class="wrap">
            <div class="home-atp">
                <div class="home-atp-copy">
                    <p class="kicker">PMI Authorized Training Partner</p>
                    <h2>Learn with <em>PMI Authorized Trainers.</em></h2>
                    <p class="lede">EdHut delivers PMI-approved instruction online and in person. {{ $site->get('stat_students') }} professionals trained, including {{ $site->get('stat_pmp') }} PMP, {{ $site->get('stat_pgmp') }} PgMP, and {{ $site->get('stat_pfmp') }} PfMP.</p>
                    <div class="home-atp-badge">
                        <img src="{{ asset('assets/images/brand/pmi-atp-authorized.png') }}" width="132" height="132" alt="PMI Authorized Training Partner badge" loading="lazy">
                        <div>
                            <strong>PMI Authorized Training Partner</strong>
                            <span class="home-atp-tag">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 3l7 3v5c0 4.5-3 8.4-7 10-4-1.6-7-5.5-7-10V6l7-3z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><path d="M9 12l2 2 4-4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                PMI-approved instruction
                            </span>
                        </div>
                    </div>
                </div>
                <div class="home-points">
                    <a class="home-point" href="{{ route('pages.show', 'about') }}">
                        <span class="home-icon">@include('components.public.service-icon', ['slug' => 'exam-simulation-tool'])</span>
                        <span class="home-point-text">
                            <strong>PMI Authorized Training Partner</strong>
                            <span>Instruction follows the published PMI standards for each credential.</span>
                        </span>
                    </a>
                    <a class="home-point" href="{{ route('courses.index') }}">
                        <span class="home-icon">@include('components.public.service-icon', ['slug' => 'self-learning-course'])</span>
                        <span class="home-point-text">
                            <strong>Live cohorts and self-paced study</strong>
                            <span>Join a scheduled cohort or study with the self-learning course.</span>
                        </span>
                    </a>
                    <a class="home-point" href="{{ route('services.index') }}">
                        <span class="home-icon">@include('components.public.service-icon', ['slug' => 'application-writing-support'])</span>
                        <span class="home-point-text">
                            <strong>Application and exam support</strong>
                            <span>Writing help, practice questions, PDUs, and a short advisory call.</span>
                        </span>
                    </a>
                    <a class="home-point" href="{{ route('services.show', 'corporate-training') }}">
                        <span class="home-icon">@include('components.public.service-icon', ['slug' => 'corporate-training'])</span>
                        <span class="home-point-text">
                            <strong>Programs for corporate teams</strong>
                            <span>{{ $site->get('stat_corporate') }} corporate cohorts, online or in your workplace.</span>
                        </span>
                    </a>
                </div>
            </div>
        </div>
    </section>

    @php $tone(); @endphp
    @include('public.partials.voices')

    @include('public.partials.roadmap', ['roadmapCourses' => $courses, 'roadmapTone' => $tone()])

    @if ($videoStories->isNotEmpty())
        <section class="section home-stories {{ $tone() }}">
            <div class="wrap">
                <p class="kicker">Video stories</p>
                <h2>Hear it from our alumni.</h2>
                <p class="lede">EdHut alumni talk about how they prepared for their PMI certification.</p>
                <div class="story-grid" data-shot-track data-shot-label="Show story">
                    @foreach ($videoStories as $story)
                        @php $youtube = $story->youtubeId(); $thumb = $story->thumbnailUrl(); @endphp
                        <a class="story-card" href="{{ $story->video_url }}" target="_blank" rel="noopener noreferrer" @if ($youtube) data-story-youtube="{{ $youtube }}" @endif aria-label="Watch {{ $story->name }}'s story">
                            <span class="story-media">
                                @if ($thumb)
                                    <img src="{{ $thumb }}" alt="" loading="lazy">
                                @endif
                                @if ($story->duration)
                                    <span class="story-time">{{ $story->duration }}</span>
                                @endif
                                <span class="story-play" aria-hidden="true">
                                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="9.5" stroke="currentColor" stroke-width="1.6"/><path d="m10 8.6 5.2 3.4-5.2 3.4V8.6Z" fill="currentColor"/></svg>
                                </span>
                                <span class="story-person">
                                    <strong>{{ $story->name }}</strong>
                                    @if ($story->designation)
                                        <span>{{ $story->designation }}</span>
                                    @endif
                                </span>
                            </span>
                            <span class="story-body">
                                @if ($story->quote)
                                    <span class="story-quote">{{ $story->quote }}</span>
                                @endif
                                <span class="story-link">Watch the story <span aria-hidden="true">→</span></span>
                            </span>
                        </a>
                    @endforeach
                </div>
                <div class="shot-dots" data-shot-dots></div>
            </div>
            <dialog class="story-dialog" data-story-dialog aria-label="Video story">
                <button class="story-close" type="button" data-story-close aria-label="Close video">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M6 6l12 12M18 6 6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                </button>
                <div class="story-frame" data-story-frame></div>
            </dialog>
        </section>
    @endif

    @if ($cohorts->isNotEmpty())
        <section class="section home-cohorts {{ $tone() }}">
            <div class="wrap">
                <p class="kicker">Upcoming cohorts</p>
                <h2>Join the next live PMI cohort.</h2>
                <p class="lede">Instructor-led batches with PMI Authorized Trainers. Pick a date and reserve your seat from the course page.</p>
                <div class="cohort-grid" data-shot-track data-shot-label="Show cohort">
                    @foreach ($cohorts as $cohort)
                        @php $course = $cohort->course; @endphp
                        <article class="cohort-card">
                            <div class="cohort-tags">
                                <span class="roadmap-code">{{ $course->shortLabel() }}</span>
                                @if ($cohort->hasStarted())
                                    <span class="cohort-live">In progress</span>
                                @endif
                            </div>
                            <h3>{{ $course->shortLabel() }} cohort, {{ $cohort->starts_on->format('F Y') }}</h3>
                            <ul class="cohort-facts">
                                <li>
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true"><rect x="4" y="5" width="16" height="15" rx="2" stroke="currentColor" stroke-width="1.7"/><path d="M8 3v4M16 3v4M4 10h16" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
                                    {{ $cohort->hasStarted() ? 'Started' : 'Starts' }} {{ $cohort->starts_on->format('j M Y') }}@if ($cohort->ends_on), ends {{ $cohort->ends_on->format('j M Y') }}@endif
                                </li>
                                @if ($cohort->hours_label)
                                    <li>
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="12" cy="12" r="8" stroke="currentColor" stroke-width="1.7"/><path d="M12 8v4l3 2" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
                                        {{ $cohort->hours_label }}
                                    </li>
                                @endif
                                @if ($cohort->time_label)
                                    <li>
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true"><rect x="3.5" y="5" width="17" height="11" rx="1.8" stroke="currentColor" stroke-width="1.7"/><path d="M8 20h8M12 16v4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
                                        {{ $cohort->time_label }}
                                    </li>
                                @endif
                                @if ($cohort->delivery_mode)
                                    <li>
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M3 9l9-4.5L21 9l-9 4.5L3 9zM7 11v4.5c0 1.4 2.2 2.5 5 2.5s5-1.1 5-2.5V11" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/></svg>
                                        {{ $cohort->delivery_mode }}
                                    </li>
                                @endif
                                <li>
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="12" cy="8" r="3.5" stroke="currentColor" stroke-width="1.7"/><path d="M5 20c.8-3.3 3.6-5 7-5s6.2 1.7 7 5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
                                    Trainer: {{ $cohort->trainerName() }}
                                </li>
                            </ul>
                            @php $cohortEntry = $course->entryPackage(); @endphp
                            <div class="cohort-price">
                                @if ($cohortEntry)
                                    <small>From</small>
                                    @if ($cohortEntry->discountPercent() > 0)
                                        <s>{{ $cohortEntry->money($cohortEntry->compare_price) }}</s>
                                    @endif
                                    <strong>{{ $cohortEntry->money($cohortEntry->price) }}</strong>
                                    @if ($cohortEntry->discountPercent() > 0)
                                        <em class="cohort-off">{{ $cohortEntry->discountPercent() }}% OFF</em>
                                    @endif
                                @else
                                    <small>Fee</small>
                                    <strong>{{ $course->priceLabel() }}</strong>
                                @endif
                            </div>
                            @if ($cohortEntry)
                                <button class="btn btn-primary cohort-btn" type="button" data-home-modal-open="cohort-modal-{{ $cohort->id }}" aria-haspopup="dialog">
                                    Reserve seat
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </button>
                            @else
                                <a class="btn btn-primary cohort-btn" href="{{ route('courses.show', $course) }}">
                                    Reserve seat
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </a>
                            @endif
                        </article>
                    @endforeach
                </div>
                <div class="shot-dots" data-shot-dots></div>
            </div>
        </section>

        @foreach ($cohorts->filter(fn ($cohort) => $cohort->course->entryPackage()) as $cohort)
            @php
                $course = $cohort->course;
                $pickedTier = $course->packages->firstWhere('is_popular', true) ?? $course->entryPackage();
            @endphp
            <div class="svc-modal" id="cohort-modal-{{ $cohort->id }}" data-home-modal hidden>
                <button class="svc-modal-scrim" type="button" data-home-modal-close tabindex="-1" aria-label="Close"></button>
                <form class="ck-dialog" method="post" action="{{ route('cart.store', $course) }}" role="dialog" aria-modal="true" aria-labelledby="cohort-modal-title-{{ $cohort->id }}">
                    @csrf
                    <input type="hidden" name="schedule" value="{{ $cohort->id }}">
                    <header class="ck-head">
                        <div>
                            <p class="kicker">Reserve your seat</p>
                            <h2 id="cohort-modal-title-{{ $cohort->id }}">Choose your package</h2>
                        </div>
                        <button class="ck-x" type="button" data-home-modal-close aria-label="Close">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M6 6l12 12M18 6 6 18" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                        </button>
                    </header>
                    <div class="ck-body">
                        <div class="ck-cohort">
                            <div>
                                <small>Selected cohort</small>
                                <strong>{{ $course->shortLabel() }} cohort, {{ $cohort->starts_on->format('F Y') }}</strong>
                            </div>
                            <div>
                                <span>{{ $cohort->rangeLabel() }}</span>
                                @if ($cohort->time_label)
                                    <span>{{ $cohort->time_label }}</span>
                                @endif
                            </div>
                            <div>
                                <span>Trainer: {{ $cohort->trainerName() }}</span>
                                @if ($cohort->delivery_mode)
                                    <span>{{ $cohort->delivery_mode }}</span>
                                @endif
                            </div>
                        </div>
                        <fieldset class="ck-packages ck-packages-{{ min($course->packages->count(), 3) }}">
                            <legend class="visually-hidden">Package</legend>
                            @foreach ($course->packages as $tier)
                                @php $tierOff = $tier->discountPercent(); @endphp
                                <div class="ck-pkg {{ $tier->is_popular ? 'is-popular' : '' }}">
                                    @if ($tier->is_popular)
                                        <span class="ck-flag">Popular</span>
                                    @endif
                                    <label class="ck-pkg-pick">
                                        <input type="radio" name="package" value="{{ $tier->id }}" @checked($tier->is($pickedTier))>
                                        <span class="ck-radio" aria-hidden="true"></span>
                                        <span class="ck-pkg-name">{{ $tier->name }}</span>
                                        <span class="ck-pkg-price">
                                            @if ($tierOff > 0)
                                                <s>{{ $tier->money($tier->compare_price) }}</s>
                                            @endif
                                            <strong>{{ $tier->money($tier->price) }}</strong>
                                            @if ($tierOff > 0)
                                                <em>{{ $tierOff }}% OFF</em>
                                            @endif
                                        </span>
                                    </label>
                                    @if ($tier->includes_label)
                                        <p class="ck-pkg-lead">{{ $tier->includes_label }}</p>
                                    @endif
                                    @if ($tier->featureList() !== [])
                                        <ul class="ck-pkg-list">
                                            @foreach ($tier->featureList() as $tierBenefit)
                                                <li>{{ $tierBenefit }}</li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </div>
                            @endforeach
                        </fieldset>
                    </div>
                    <footer class="ck-foot">
                        <button class="btn btn-ghost" type="button" data-home-modal-close>Cancel</button>
                        <button class="btn ck-cart" type="submit">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M3 4h2l2.2 10.2a1.5 1.5 0 0 0 1.5 1.2h8.6a1.5 1.5 0 0 0 1.5-1.1L21 8H6.2" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/><circle cx="9.5" cy="19.5" r="1.3" fill="currentColor"/><circle cx="17" cy="19.5" r="1.3" fill="currentColor"/></svg>
                            Add to Cart
                        </button>
                        <button class="btn btn-primary" type="submit" name="buy_now" value="1">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M13 3 5 13.5h6L10 21l8-10.5h-6L13 3z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/></svg>
                            Buy Now
                        </button>
                    </footer>
                </form>
            </div>
        @endforeach
    @endif

    @php $outcomes = \App\Support\Content::get('home.outcomes'); @endphp
    <section class="section home-outcomes {{ $tone() }}">
        <div class="wrap">
            <p class="kicker">{{ $outcomes['kicker'] }}</p>
            <h2>{{ $outcomes['title'] }}</h2>
            <p class="lede">{{ $outcomes['lede'] }}</p>
            <div class="outcome-grid" data-shot-track data-shot-label="Show outcome">
                @foreach ($outcomes['items'] as $outcome)
                    <article class="outcome-card">
                        <span class="roadmap-code">{{ $outcome['code'] }}</span>
                        <h3>{{ $outcome['title'] }}</h3>
                        <p class="outcome-label">Career impact</p>
                        <strong class="outcome-stat">{{ $outcome['stat'] }}</strong>
                        <p class="outcome-note">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 17l6-6 4 4 6-7M14 8h6v6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            {{ $outcome['note'] }}
                        </p>
                    </article>
                @endforeach
            </div>
            <div class="shot-dots" data-shot-dots></div>
            <ul class="outcome-facts">
                @foreach ($outcomes['facts'] as $outcomeFact)
                    <li>
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" aria-hidden="true">@include('components.public.icon', ['name' => $outcomeFact['icon']])</svg>
                        {{ $outcomeFact['text'] }}
                    </li>
                @endforeach
            </ul>
        </div>
    </section>

    @include('public.partials.services', ['servicesTone' => $tone()])

    @include('public.partials.founder')

    @php $capsules = \App\Support\Content::get('home.capsules'); @endphp
    <section class="section home-capsules {{ $tone() }}">
        <div class="wrap">
            <p class="kicker">{{ $capsules['kicker'] }}</p>
            <h2>{{ $capsules['title'] }}</h2>
            <p class="lede">{{ $capsules['lede'] }}</p>
            <div class="capsule-grid" data-shot-track data-shot-label="Show insight">
                @foreach ($capsules['items'] as $capsule)
                    <article class="capsule-card">
                        <span class="capsule-icon" aria-hidden="true">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none">@include('components.public.icon', ['name' => 'bulb'])</svg>
                        </span>
                        <h3>{{ $capsule['title'] }}</h3>
                        <p>{{ $capsule['text'] }}</p>
                    </article>
                @endforeach
            </div>
            <div class="shot-dots" data-shot-dots></div>
        </div>
    </section>

    @php $appBlock = \App\Support\Content::get('home.app'); @endphp
    <section class="section home-block {{ $tone() }}">
        <div class="wrap home-app">
            <div class="home-app-copy">
                <p class="kicker">{{ $appBlock['kicker'] }}</p>
                <h2>{{ $appBlock['title'] }}</h2>
                <p class="home-app-lede">{{ $appBlock['lede'] }}</p>
                <ul class="home-app-features">
                    @foreach ($appBlock['features'] as $appFeature)
                        <li>
                            <span aria-hidden="true"><svg width="16" height="16" viewBox="0 0 24 24" fill="none">@include('components.public.icon', ['name' => $appFeature['icon']])</svg></span>
                            {{ $appFeature['label'] }}
                        </li>
                    @endforeach
                </ul>
                <div class="home-app-stores">
                    <a class="store-btn" href="#">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M16.37 12.6c-.02-2.2 1.8-3.26 1.88-3.31-1.03-1.5-2.62-1.7-3.18-1.73-1.35-.14-2.64.8-3.33.8-.69 0-1.74-.78-2.86-.76-1.47.02-2.83.86-3.59 2.17-1.53 2.66-.39 6.6 1.1 8.75.73 1.05 1.6 2.24 2.73 2.2 1.1-.05 1.51-.71 2.84-.71 1.32 0 1.7.71 2.86.69 1.18-.02 1.93-1.07 2.65-2.13.84-1.22 1.18-2.4 1.2-2.46-.03-.01-2.3-.88-2.32-3.5zM14.2 6.13c.6-.73 1.01-1.75.9-2.76-.87.04-1.92.58-2.54 1.31-.56.64-1.05 1.68-.92 2.67.97.08 1.96-.49 2.56-1.22z"/></svg>
                        <span><small>Download on the</small>App Store</span>
                    </a>
                    <a class="store-btn" href="#">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M4.2 2.7c-.25.27-.4.68-.4 1.2v16.2c0 .52.15.93.4 1.2l.07.06L13.4 12.2v-.2L4.27 2.64l-.07.06zM16.45 15.27l-3.05-3.05v-.2l3.05-3.05.07.04 3.61 2.05c1.03.59 1.03 1.54 0 2.13l-3.61 2.05-.07.03zM16.52 15.24L13.4 12.1 4.2 21.3c.34.36.9.4 1.53.05l10.79-6.11M16.52 8.97L5.73 2.86c-.63-.36-1.19-.31-1.53.05l9.2 9.2 3.12-3.14z"/></svg>
                        <span><small>Get it on</small>Google Play</span>
                    </a>
                </div>
                <p class="home-app-foot">{{ $appBlock['foot'] }}</p>
            </div>

            <div class="app-phone" aria-hidden="true">
                <div class="app-phone-screen">
                    <span class="app-phone-notch"></span>
                    <div class="app-phone-bar">
                        <strong>EdHut</strong>
                        <span class="app-phone-avatar">E</span>
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none"><path d="M6 16V11a6 6 0 1112 0v5l1.5 2h-15L6 16zM10 20.5a2 2 0 004 0" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/></svg>
                    </div>
                    <div class="app-phone-welcome">
                        <small>Welcome back!</small>
                        <strong>Keep learning, keep growing.</strong>
                        <span>Stay consistent. Stay exam ready.</span>
                        <i></i>
                    </div>
                    <p class="app-phone-label">Explore</p>
                    <ul class="app-phone-list">
                        @foreach ($appBlock['screens'] as $appScreen)
                            <li>
                                <span class="app-phone-icon"><svg width="13" height="13" viewBox="0 0 24 24" fill="none">@include('components.public.icon', ['name' => $appScreen['icon']])</svg></span>
                                <span><strong>{{ $appScreen['title'] }}</strong><small>{{ $appScreen['text'] }}</small></span>
                                <svg width="10" height="10" viewBox="0 0 24 24" fill="none"><path d="M9 6l6 6-6 6" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </li>
                        @endforeach
                    </ul>
                    <nav class="app-phone-nav">
                        <span class="is-active"><svg width="14" height="14" viewBox="0 0 24 24" fill="none"><path d="M4 11l8-6.5 8 6.5V20h-5v-5h-6v5H4v-9z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/></svg>Home</span>
                        <span><svg width="14" height="14" viewBox="0 0 24 24" fill="none"><path d="M5 20V10M12 20V4M19 20v-7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>My Progress</span>
                        <span><svg width="14" height="14" viewBox="0 0 24 24" fill="none"><path d="M7 4h10v16l-5-3.5L7 20V4z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/></svg>Bookmarks</span>
                        <span><svg width="14" height="14" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="8.5" r="3.5" stroke="currentColor" stroke-width="1.8"/><path d="M5 19.5c1-3.2 3.7-5 7-5s6 1.8 7 5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>Profile</span>
                    </nav>
                </div>
            </div>
        </div>
    </section>

    @php $corp = \App\Support\Content::get('home.corporate'); @endphp
    <section class="section home-corp {{ $tone() }}">
        <div class="wrap home-corp-grid">
            <div class="home-corp-copy">
                <p class="kicker">{{ $corp['kicker'] }}</p>
                <h2>{{ $corp['title'] }}</h2>
                <p class="lede">{{ $corp['lede'] }}</p>
                <div class="hero-actions">
                    <a class="btn btn-primary" href="{{ route('services.show', 'corporate-training') }}#plan-cohort">
                        Design a Corporate Cohort
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </a>
                    <a class="btn btn-ghost" href="{{ route('contact', ['message' => 'I would like a proposal for corporate PMI training.']) }}">Request Proposal</a>
                </div>
            </div>
            <div class="home-corp-cards">
                @foreach ($corp['items'] as $corpTrack)
                    <article class="home-corp-card">
                        <span class="home-corp-icon" aria-hidden="true"><svg width="16" height="16" viewBox="0 0 24 24" fill="none">@include('components.public.icon', ['name' => $corpTrack['icon']])</svg></span>
                        <h3>{{ $corpTrack['title'] }}</h3>
                        <p>{{ $corpTrack['text'] }}</p>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    @if ($blogs->isNotEmpty())
        <section class="section home-block {{ $tone() }}">
            <div class="wrap">
                <div class="section-head">
                    <h2>Recent articles.</h2>
                    <a class="home-more" href="{{ route('blogs.index') }}">All articles</a>
                </div>
                <div class="home-blogs">
                    @foreach ($blogs as $blog)
                        <a class="home-card home-blog" href="{{ route('blogs.show', $blog) }}">
                            @if ($blog->image)
                                <img src="{{ asset($blog->image) }}" alt="{{ $blog->image_alt ?: $blog->title }}" loading="lazy">
                            @endif
                            <span>
                                @if ($blog->category)<em>{{ $blog->category }}</em>@endif
                                <h3>{{ $blog->title }}</h3>
                                <p>{{ $blog->excerpt }}</p>
                            </span>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @include('components.public.cta-band')
@endsection
