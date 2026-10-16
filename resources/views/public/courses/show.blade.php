@extends('layouts.public')

@section('toast_errors', '1')

@push('head')
    <link href="{{ asset('assets/css/services.css') }}?v={{ @filemtime(public_path('assets/css/services.css')) }}" rel="stylesheet">
    <link href="{{ asset('assets/css/courses.css') }}?v={{ @filemtime(public_path('assets/css/courses.css')) }}" rel="stylesheet">
    <link href="{{ asset('assets/css/showcase.css') }}?v={{ @filemtime(public_path('assets/css/showcase.css')) }}" rel="stylesheet">
    <link href="{{ asset('assets/css/sections.css') }}?v={{ @filemtime(public_path('assets/css/sections.css')) }}" rel="stylesheet">
@endpush

@section('content')
    @php
        $entryPackage = $course->packages->sortBy(fn ($package) => (float) $package->price)->first();
    @endphp
    <section class="svc-hero svc-detail-hero has-world-map">
        <div class="wrap svc-detail-grid">
            <div>
                <p class="svc-crumb"><a href="{{ route('courses.index') }}">Courses</a> <span aria-hidden="true">/</span> {{ $course->shortLabel() }}</p>
                <p class="svc-pill"><span class="dot" aria-hidden="true"></span> PMI Authorized Training Partner</p>
                <h1>{{ $course->title }}</h1>
                <p>{{ $course->short_description }}</p>
                @if ($enrolled)
                    <p class="course-owned"><span aria-hidden="true">✓</span> You already bought this course</p>
                @elseif ($pendingOrder)
                    <p class="course-owned is-wait"><span aria-hidden="true">✓</span> Already purchased · waiting for approval</p>
                @endif
                <div class="hero-actions">
                    @if ($enrolled)
                        <a class="btn btn-primary" href="{{ route('learn.show', $course) }}">Continue learning <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg></a>
                    @elseif ($pendingOrder)
                        <a class="btn btn-primary" href="{{ route('account.orders.show', $pendingOrder) }}">View my order</a>
                    @elseif ($inCart)
                        <a class="btn btn-primary" href="{{ route('cart.index') }}">View cart</a>
                    @elseif ($course->packages->isNotEmpty())
                        <a class="btn btn-primary" href="#packages">Choose a package <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 5v14M6 13l6 6 6-6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg></a>
                    @else
                        <form method="post" action="{{ route('cart.store', $course) }}">
                            @csrf
                            <button class="btn btn-primary" type="submit">Add to cart <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg></button>
                        </form>
                    @endif
                    <a class="btn btn-ghost" href="{{ route('contact', ['message' => 'I would like a quotation for '.$course->title.'.']) }}">Request quotation</a>
                </div>
            </div>
            @php
                $badgeArea = trim(\Illuminate\Support\Str::before($course->title, ' Professional')) ?: 'PMI Certification';
                $nodes = [
                    ['Application Review', '<path d="M16 19v-1.5a3.5 3.5 0 00-3.5-3.5h-5A3.5 3.5 0 004 17.5V19M10 10.5a3.5 3.5 0 100-7 3.5 3.5 0 000 7zM20 19v-1.5a3.5 3.5 0 00-2.5-3.35M15.5 3.65a3.5 3.5 0 010 6.7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>'],
                    ['Exam Simulation Practice', '<circle cx="12" cy="12" r="8.5" stroke="currentColor" stroke-width="1.8"/><circle cx="12" cy="12" r="5" stroke="currentColor" stroke-width="1.8"/><circle cx="12" cy="12" r="1.6" fill="currentColor"/>'],
                    [preg_match('/\d/', (string) $course->duration) ? \Illuminate\Support\Str::title($course->duration).' of Training' : 'PMI Authorized Trainers', '<path d="M12 6.5C10.5 5 8 4.5 4 4.5v13c4 0 6.5.5 8 2 1.5-1.5 4-2 8-2v-13c-4 0-6.5.5-8 2zM12 6.5v13" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>'],
                    ['Latest Published Standard', '<path d="M4 17l6-6 4 4 6-7M14 8h6v6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>'],
                ];
            @endphp
            <div class="crs-orbit">
                <span class="crs-orbit-glow" aria-hidden="true"></span>
                <span class="crs-orbit-inner" aria-hidden="true"></span>
                <div class="crs-orbit-track">
                    @foreach ($nodes as [$label, $icon])
                        <div class="crs-node crs-node-{{ $loop->iteration }}">
                            <div class="crs-node-spin">
                                <div class="crs-node-body">
                                    <span class="crs-node-icon" aria-hidden="true"><svg width="22" height="22" viewBox="0 0 24 24" fill="none">{!! $icon !!}</svg></span>
                                    <span class="crs-node-label">{{ $label }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="crs-badge" role="img" aria-label="{{ $course->shortLabel() }} {{ $badgeArea }} Professional certification">
                    <svg class="crs-badge-ring" viewBox="0 0 200 200" aria-hidden="true">
                        <defs>
                            <path id="crs-arc-top" d="M 17,100 A 83,83 0 0 1 183,100"/>
                            <path id="crs-arc-bottom" d="M 8,100 A 92,92 0 0 0 192,100"/>
                        </defs>
                        <text><textPath href="#crs-arc-top" startOffset="50%" text-anchor="middle">{{ strtoupper($badgeArea) }}</textPath></text>
                        <text><textPath href="#crs-arc-bottom" startOffset="50%" text-anchor="middle">PROFESSIONAL</textPath></text>
                    </svg>
                    <div class="crs-badge-face">
                        <span class="crs-badge-code">{{ $course->shortLabel() }}<sup>®</sup></span>
                        <span class="crs-badge-title">Professional Certification</span>
                        <span class="crs-badge-pmi"></span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @php
        $courseMark = $course->shortLabel().'®';
        $band = 0;
        $tone = function () use (&$band) {
            return ++$band % 2 ? 'band-tint' : 'band-plain';
        };
        $details = \App\Support\Content::get('courses.details');
        $detailParagraphs = $course->overviewParagraphs();
        $detailObjectives = $course->objectiveList();
        $detailModules = $course->sections->filter(fn ($agendaModule) => $agendaModule->lessons->isNotEmpty())->values();
        $detailTabs = array_filter([
            'overview' => $detailParagraphs !== [] ? 'book' : null,
            'objectives' => $detailObjectives !== [] ? 'clipboard' : null,
            'agenda' => $detailModules->isNotEmpty() ? 'clipboard-check' : null,
            'resources' => $course->resources->isNotEmpty() ? 'document' : null,
        ]);
        $subnav = \App\Support\Content::get('courses.subnav');
        $subnavShown = [
            'why-edhut' => ! empty(\App\Support\Content::get('courses.why.courses')[$course->slug] ?? []),
            'packages' => $course->packages->isNotEmpty(),
            'cohorts' => true,
            'course-details' => $detailTabs !== [],
            'trainers' => $trainers->isNotEmpty(),
            'hall-of-fame' => $alumni->isNotEmpty(),
            'faqs' => $course->faqs->isNotEmpty(),
        ];
    @endphp
    <nav class="crs-subnav" aria-label="{{ $subnav['label'] }}" data-subnav>
        <div class="wrap crs-subnav-inner">
            <ul class="crs-subnav-links">
                @foreach ($subnav['links'] as $anchor => $label)
                    @if ($subnavShown[$anchor] ?? false)
                        <li><a href="#{{ $anchor }}">{{ $label }}</a></li>
                    @endif
                @endforeach
            </ul>
            @if ($enrolled)
                <a class="btn btn-primary crs-subnav-cta" href="{{ route('learn.show', $course) }}">{{ $subnav['continue'] }} <svg width="14" height="14" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg></a>
            @else
                <a class="btn btn-primary crs-subnav-cta" href="#enroll">{{ $subnav['enroll'] }} <svg width="14" height="14" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg></a>
            @endif
        </div>
    </nav>

    @if ($alumni->isNotEmpty())
        @php
            $fame = \App\Support\Content::get('courses.fame');
            $fameStatKey = $fame['stats'][$course->slug] ?? null;
            $fameText = fn (string $text) => str_replace([':course', ':students'], [$courseMark, $site->get('stat_students')], $text);
        @endphp
        <section class="section {{ $tone() }} crs-fame" id="hall-of-fame">
            <div class="wrap">
                <div class="crs-fame-head">
                    <div class="crs-block-head">
                        <p class="kicker crs-fame-kicker">
                            <span aria-hidden="true"><svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M8 4h8v5a4 4 0 0 1-8 0V4zM8 6H5v1.5A3 3 0 0 0 8 10.5M16 6h3v1.5a3 3 0 0 1-3 3M12 13v4M8.5 20h7M10 17h4v3h-4z" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
                            {{ $fame['kicker'] }}
                        </p>
                        <h2>{{ $fameText($fame['title']) }}</h2>
                        <p>{{ $fameText($fame['lede']) }}</p>
                    </div>
                    <div class="crs-fame-stats">
                        @if ($fameStatKey && filled($site->get($fameStatKey)))
                            <div class="crs-fame-stat">
                                <span class="crs-fame-stat-icon" aria-hidden="true"><svg width="18" height="18" viewBox="0 0 24 24" fill="none">@include('components.public.icon', ['name' => 'users'])</svg></span>
                                <p><strong>{{ $site->get($fameStatKey) }}</strong><span>{{ $fameText($fame['stat_course']) }}</span><small>{{ $fame['stat_course_note'] }}</small></p>
                            </div>
                        @endif
                        <div class="crs-fame-stat">
                            <span class="crs-fame-stat-icon" aria-hidden="true"><svg width="18" height="18" viewBox="0 0 24 24" fill="none">@include('components.public.icon', ['name' => 'globe'])</svg></span>
                            <p><strong>{{ $site->get('stat_students') }}</strong><span>{{ $fame['stat_students'] }}</span><small>{{ $fame['stat_students_note'] }}</small></p>
                        </div>
                    </div>
                </div>
                <div class="crs-fame-body">
                    <aside class="crs-fame-aside">
                        <div class="crs-fame-quote">
                            <span class="crs-fame-quote-mark" aria-hidden="true">&ldquo;</span>
                            <h3>{{ $fame['aside_title'] }}</h3>
                            <p>{{ $fame['aside_text'] }}</p>
                            <span class="crs-fame-quote-seal" aria-hidden="true"><svg width="18" height="18" viewBox="0 0 24 24" fill="none">@include('components.public.icon', ['name' => 'shield'])</svg></span>
                        </div>
                        <ul class="crs-fame-points">
                            @foreach ($fame['points'] as $point)
                                <li>
                                    <span aria-hidden="true"><svg width="14" height="14" viewBox="0 0 24 24" fill="none">@include('components.public.icon', ['name' => $point['icon']])</svg></span>
                                    <p><strong>{{ $point['title'] }}</strong>{{ $fameText($point['text']) }}</p>
                                </li>
                            @endforeach
                        </ul>
                    </aside>
                    <div>
                        <div class="crs-fame-grid" data-shot-track data-shot-label="Show certified professional">
                            @foreach ($alumni as $alumnus)
                                <article class="crs-fame-card">
                                    <div class="crs-fame-photo">
                                        @if (filled($alumnus->image))
                                            <img src="{{ asset($alumnus->image) }}" alt="{{ $alumnus->name }}" loading="lazy">
                                        @else
                                            <span class="crs-fame-initials" aria-hidden="true">{{ $alumnus->initials() }}</span>
                                        @endif
                                        <span class="crs-fame-seal" aria-hidden="true"><svg width="12" height="12" viewBox="0 0 24 24" fill="none">@include('components.public.icon', ['name' => 'shield'])</svg></span>
                                    </div>
                                    <div class="crs-fame-info">
                                        <h3>{{ $alumnus->name }}</h3>
                                        @if (filled($alumnus->job_title))
                                            <p>{{ $alumnus->job_title }}</p>
                                        @endif
                                        <span class="crs-fame-badge">
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2"/><path d="m8.5 12.2 2.4 2.4 4.6-4.9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                            {{ $fameText($fame['badge']) }}
                                        </span>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                        <div class="shot-dots" data-shot-dots></div>
                    </div>
                </div>
            </div>
        </section>
    @endif

    @php
        $journey = \App\Support\Content::get('courses.journey');
        $journeyCourse = $journey['courses'][$course->slug] ?? null;
    @endphp
    @if ($journeyCourse !== null)
        <section class="section {{ $tone() }} crs-journey" id="journey">
            <div class="wrap">
                <div class="crs-block-head">
                    <p class="kicker">{{ $journey['kicker'] }}</p>
                    <h2>{{ $journey['title'] }} <span>{{ str_replace(':course', $courseMark, $journey['accent']) }}</span></h2>
                    <p>{{ $journey['lede'] }}</p>
                </div>
                <ol class="crs-journey-steps">
                    @foreach ($journey['steps'] as $step)
                        @php $step = array_merge($step, $journeyCourse[$step['key']] ?? []); @endphp
                        <li class="crs-journey-step">
                            <div class="crs-journey-node">
                                <span class="crs-journey-num">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                                <span class="crs-journey-icon" aria-hidden="true"><svg width="26" height="26" viewBox="0 0 24 24" fill="none">@include('components.public.icon', ['name' => $step['icon']])</svg></span>
                                <span class="crs-journey-link {{ $loop->last ? 'is-flag' : '' }}" aria-hidden="true">
                                    @if ($loop->last)
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none"><path d="M6 21V4h11l-2.5 4.5L17 13H6" fill="currentColor" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/></svg>
                                    @else
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none"><path d="M6 7l5 5-5 5M12 7l5 5-5 5" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    @endif
                                </span>
                            </div>
                            <div class="crs-journey-card">
                                <h3>{{ str_replace(':course', $courseMark, $step['title']) }}</h3>
                                <p>{{ str_replace(':course', $courseMark, $step['text']) }}</p>
                            </div>
                        </li>
                    @endforeach
                </ol>
                <div class="crs-journey-banner">
                    <span class="crs-journey-rocket" aria-hidden="true"><svg width="22" height="22" viewBox="0 0 24 24" fill="none"><path d="M12 15l-3-3a22 22 0 0 1 9-8.5c1-.4 2.2-.5 3.5-.5 0 1.3-.1 2.5-.5 3.5A22 22 0 0 1 12 15z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/><path d="M9 12H5.5l2-3.5H11M12 15v3.5l3.5-2V13M7 16.5c-1.5.5-2.5 2-2.5 3.5 1.5 0 3-1 3.5-2.5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/><circle cx="16" cy="8" r="1.4" fill="currentColor"/></svg></span>
                    <p><strong>{{ $journey['banner']['title'] }}</strong> <span>{{ $journey['banner']['text'] }}</span></p>
                </div>
            </div>
        </section>
    @endif

    @php
        $overview = \App\Support\Content::get('courses.overview');
        $overviewCourse = $overview['courses'][$course->slug] ?? null;
    @endphp
    @if ($overviewCourse)
        <section class="section {{ $tone() }} crs-overview" id="overview">
            <div class="wrap">
                <div class="crs-overview-head">
                    <p class="kicker">{{ $overview['kicker'] }}</p>
                    <h2>{{ $overview['title'] }}</h2>
                    <p>{{ $overviewCourse['lede'] }}</p>
                </div>
                <div class="crs-overview-grid">
                    @foreach ($overview['cards'] as $cardKey => $cardTitle)
                        @if (! empty($overviewCourse[$cardKey]))
                            <details class="crs-overview-card" open data-overview-card>
                                <summary>
                                    {{ $cardTitle }}
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m6 9 6 6 6-6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </summary>
                                <ul>
                                    @foreach ($overviewCourse[$cardKey] as $point)
                                        <li>
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.8"/><path d="m8.5 12.2 2.4 2.4 4.6-4.9" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                            {{ $point }}
                                        </li>
                                    @endforeach
                                </ul>
                            </details>
                        @endif
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @php
        $capsules = \App\Support\Content::get('courses.capsules');
        $capsuleItems = $capsules['courses'][$course->slug] ?? [];
        $why = \App\Support\Content::get('courses.why');
        $whyItems = $why['courses'][$course->slug] ?? [];
    @endphp
    @if ($capsuleItems !== [])
        <section class="section crs-capsules {{ $tone() }}" id="quick-facts">
            <div class="wrap">
                <div class="crs-block-head">
                    <p class="kicker">{{ $capsules['kicker'] }}</p>
                    <h2>{{ str_replace(':course', $courseMark, $capsules['title']) }}</h2>
                    <p>{{ $capsules['lede'] }}</p>
                </div>
                <div class="crs-capsule-grid" data-shot-track data-shot-label="Show fact">
                    @foreach ($capsuleItems as $capsule)
                        <article class="crs-capsule">
                            <span class="crs-capsule-icon" aria-hidden="true"><svg width="18" height="18" viewBox="0 0 24 24" fill="none">@include('components.public.icon', ['name' => 'bulb'])</svg></span>
                            <h3>{{ $capsule['title'] }}</h3>
                            <p>{{ $capsule['text'] }}</p>
                        </article>
                    @endforeach
                </div>
                <div class="shot-dots" data-shot-dots></div>
            </div>
        </section>
    @endif

    @if ($whyItems !== [])
        <section class="section crs-why {{ $tone() }}" id="why-edhut">
            <div class="wrap">
                <div class="crs-block-head">
                    <p class="kicker">{{ str_replace(':course', $courseMark, $why['kicker']) }}</p>
                    <h2>{{ $why['title'] }}</h2>
                    <p>{{ $why['lede'] }}</p>
                </div>
                <div class="crs-why-grid">
                    @foreach ($whyItems as $whyItem)
                        <article class="crs-why-card">
                            <span class="crs-why-icon" aria-hidden="true"><svg width="18" height="18" viewBox="0 0 24 24" fill="none">@include('components.public.icon', ['name' => $whyItem['icon']])</svg></span>
                            <h3>{{ $whyItem['title'] }}</h3>
                            <p>{{ $whyItem['text'] }}</p>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if ($testimonials->isNotEmpty())
        @include('public.partials.voices', [
            'voiceTone' => $tone(),
            'voiceChip' => $course->shortLabel(),
            'voiceTitle' => 'Voices from our '.$course->shortLabel().' alumni.',
            'voiceLede' => 'Reviews from professionals who trained with EdHut for '.$course->shortLabel().' preparation.',
        ])
    @endif

    @if ($course->packages->isNotEmpty())
        <section class="section {{ $tone() }} pkg-section" id="packages">
            <div class="wrap">
                <div class="svc-section-head pkg-head">
                    <p class="kicker">Course packages</p>
                    <h2>Choose the package that matches your goal.</h2>
                    <p>Every tier includes live instructor-led training. Higher tiers add application support, mocks and renewal PDUs.</p>
                </div>
                <div class="pkg-grid pkg-grid-{{ min($course->packages->count(), 4) }}" data-shot-track data-shot-label="Show package">
                    @foreach ($course->packages as $pkg)
                        @php
                            $pkgOff = $pkg->discountPercent();
                            $pkgBenefits = $pkg->featureList();
                        @endphp
                        <article class="pkg-card {{ $pkg->is_popular ? 'is-popular' : '' }}">
                            <header class="pkg-card-head">
                                <h3>{{ $pkg->name }}</h3>
                                @if ($pkg->is_popular)
                                    <span class="pkg-flag">Most Popular</span>
                                @endif
                            </header>
                            <div class="pkg-price">
                                @if ($pkgOff > 0)
                                    <s>{{ $pkg->money($pkg->compare_price) }}</s>
                                @endif
                                <p>
                                    <strong>{{ $pkg->money($pkg->price) }}</strong>
                                    @if ($pkgOff > 0)
                                        <span class="pkg-off">{{ $pkgOff }}% OFF</span>
                                    @endif
                                </p>
                            </div>
                            @if (in_array($pkg->id, $ownedPackages, true))
                                <a class="btn btn-ghost pkg-cta" href="{{ route('learn.show', $course) }}">Continue learning</a>
                            @elseif (isset($pendingPackages[$pkg->id]))
                                <a class="btn btn-ghost pkg-cta" href="{{ route('account.orders.show', $pendingPackages[$pkg->id]) }}">View my order</a>
                            @elseif ($cartLine && (int) $cartLine->course_package_id === $pkg->id)
                                <a class="btn btn-primary pkg-cta" href="{{ route('cart.index') }}"><span aria-hidden="true">✓</span> In your cart</a>
                            @elseif ($cohorts->isNotEmpty())
                                <form method="post" action="{{ route('cart.store', $course) }}">
                                    @csrf
                                    <input type="hidden" name="package" value="{{ $pkg->id }}">
                                    <button class="btn {{ $pkg->is_popular ? 'btn-primary' : 'btn-ghost' }} pkg-cta" type="submit" data-cohort-open data-package-id="{{ $pkg->id }}">Enroll · {{ $pkg->name }}</button>
                                </form>
                            @endif
                            @if ($pkg->includes_label || $pkgBenefits !== [])
                                <div class="pkg-body">
                                    @if ($pkg->includes_label)
                                        <p class="pkg-lead">{{ $pkg->includes_label }}</p>
                                    @endif
                                    @if ($pkgBenefits !== [])
                                        <ul class="pkg-list">
                                            @foreach ($pkgBenefits as $pkgBenefit)
                                                <li>
                                                    <span class="pkg-tick" aria-hidden="true"><svg width="12" height="12" viewBox="0 0 24 24" fill="none"><path d="M5 12.5l4.5 4.5L19 7.5" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
                                                    {{ $pkgBenefit }}
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </div>
                            @endif
                        </article>
                    @endforeach
                </div>
                <div class="shot-dots" data-shot-dots></div>
                @if (filled($course->packages_note))
                    <p class="pkg-note">{{ $course->packages_note }}</p>
                @endif
            </div>
        </section>
    @endif

    @include('public.courses.partials.cohort-list', ['cohortTone' => $tone()])

    @if ($cohorts->isNotEmpty() && $course->packages->contains(fn ($pkg) => ! in_array($pkg->id, $ownedPackages, true) && ! isset($pendingPackages[$pkg->id])))
        @include('public.courses.partials.cohort-modal')
    @endif

    @if ($detailTabs !== [])
        <section class="section crs-details {{ $tone() }}" id="course-details">
            <div class="wrap">
                <div class="crs-block-head">
                    <p class="kicker">{{ $details['kicker'] }}</p>
                    <h2>{{ str_replace(':course', $courseMark, $details['title']) }}</h2>
                    <p>{{ $details['lede'] }}</p>
                </div>
                <div class="crs-tabs" data-tabs>
                    <div class="crs-tab-list" role="tablist" aria-label="{{ $details['kicker'] }}">
                        @foreach ($detailTabs as $tabKey => $tabIcon)
                            <button class="crs-tab" type="button" role="tab" id="crs-tab-{{ $tabKey }}" aria-controls="crs-panel-{{ $tabKey }}" aria-selected="{{ $loop->first ? 'true' : 'false' }}" tabindex="{{ $loop->first ? '0' : '-1' }}">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" aria-hidden="true">@include('components.public.icon', ['name' => $tabIcon])</svg>
                                {{ $details['tabs'][$tabKey] }}
                            </button>
                        @endforeach
                    </div>
                    @foreach ($detailTabs as $tabKey => $tabIcon)
                        <div class="crs-tab-panel crs-tab-panel-{{ $tabKey }}" role="tabpanel" id="crs-panel-{{ $tabKey }}" aria-labelledby="crs-tab-{{ $tabKey }}" tabindex="0" @unless ($loop->first) hidden @endunless>
                            @switch($tabKey)
                                @case('overview')
                                    <div class="crs-detail-card crs-detail-prose">
                                        @foreach ($detailParagraphs as $paragraph)
                                            <p>{{ $paragraph }}</p>
                                        @endforeach
                                    </div>
                                    @break
                                @case('objectives')
                                    <ol class="crs-detail-card crs-objectives">
                                        @foreach ($detailObjectives as $objective)
                                            <li><span aria-hidden="true">{{ $loop->iteration }}</span>{{ $objective }}</li>
                                        @endforeach
                                    </ol>
                                    @break
                                @case('agenda')
                                    <div class="crs-agenda">
                                        @foreach ($detailModules as $agendaModule)
                                            <details class="crs-agenda-card" open data-agenda-module>
                                                <summary>
                                                    <span class="crs-agenda-label">{{ $details['module'] }} {{ $loop->iteration }}</span>
                                                    <h3>{{ $agendaModule->title }}</h3>
                                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m6 9 6 6 6-6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                                </summary>
                                                <ul>
                                                    @foreach ($agendaModule->lessons as $topic)
                                                        <li>
                                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.8"/><path d="m8.5 12.2 2.4 2.4 4.6-4.9" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                                            {{ $topic->title }}
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </details>
                                        @endforeach
                                    </div>
                                    @break
                                @case('resources')
                                    <ul class="crs-detail-card crs-resources">
                                        @foreach ($course->resources as $courseResource)
                                            <li>
                                                <span class="crs-resource-icon" aria-hidden="true"><svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M14 3.5H7A1.5 1.5 0 0 0 5.5 5v14A1.5 1.5 0 0 0 7 20.5h10a1.5 1.5 0 0 0 1.5-1.5V8L14 3.5z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/><path d="M14 3.5V8h4.5M9 12.5h6M9 16h6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
                                                <div>
                                                    <h3>
                                                        @if ($courseResource->hasFile())
                                                            <a href="{{ route('courses.resource', [$course, $courseResource->id]) }}" target="_blank" rel="noopener">
                                                                {{ $courseResource->title }}
                                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 4v11M7 10.5l5 5 5-5M5 19.5h14" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                                                <span class="visually-hidden">({{ $details['open_pdf'] }})</span>
                                                            </a>
                                                        @else
                                                            {{ $courseResource->title }}
                                                        @endif
                                                    </h3>
                                                    @if (filled($courseResource->description))
                                                        <p>{{ $courseResource->description }}</p>
                                                    @endif
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                    @break
                            @endswitch
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if ($trainers->isNotEmpty())
        @php $trainerCopy = \App\Support\Content::get('courses.trainers'); @endphp
        <section class="section crs-trainers {{ $tone() }}" id="trainers">
            <div class="wrap">
                <div class="crs-block-head">
                    <p class="kicker">{{ $trainerCopy['kicker'] }}</p>
                    <h2>{{ str_replace(':course', $courseMark, $trainerCopy['title']) }}</h2>
                    <p>{{ $trainerCopy['lede'] }}</p>
                </div>
                <div class="crs-trainer-grid" data-shot-track data-shot-label="Show trainer">
                    @foreach ($trainers as $trainer)
                        <article class="crs-trainer">
                            <div class="crs-trainer-photo">
                                @if (filled($trainer->image))
                                    <img src="{{ asset($trainer->image) }}" alt="{{ $trainer->name }}" loading="lazy">
                                @else
                                    <span class="crs-trainer-initials" aria-hidden="true">{{ $trainer->initials() }}</span>
                                @endif
                            </div>
                            <div class="crs-trainer-body">
                                <h3>{{ $trainer->name }}</h3>
                                @if (filled($trainer->credentials))
                                    <p class="crs-trainer-creds">{{ $trainer->credentials }}</p>
                                @endif
                                @if (filled($trainer->experience))
                                    <p class="crs-trainer-exp">{{ $trainer->experience }}</p>
                                @endif
                                @if (filled($trainer->bio))
                                    <p class="crs-trainer-bio">{{ $trainer->bio }}</p>
                                @endif
                                @if (filled($trainer->linkedin_url))
                                    <a class="crs-trainer-link" href="{{ $trainer->linkedin_url }}" target="_blank" rel="noopener noreferrer">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M4.98 3.5a2.5 2.5 0 11-.02 5 2.5 2.5 0 01.02-5zM3 9h4v12H3V9zm7 0h3.8v1.7h.05c.53-1 1.83-2.05 3.77-2.05C21.6 8.65 22 11.3 22 14.4V21h-4v-5.8c0-1.4-.03-3.2-1.95-3.2-1.95 0-2.25 1.52-2.25 3.1V21h-4V9z"/></svg>
                                        {{ $trainerCopy['linkedin'] }} <span class="visually-hidden">profile of {{ $trainer->name }}</span>
                                    </a>
                                @endif
                            </div>
                        </article>
                    @endforeach
                </div>
                <div class="shot-dots" data-shot-dots></div>
            </div>
        </section>
    @endif

    @if (! $enrolled)
        @php
            $enroll = \App\Support\Content::get('courses.enroll');
            $certifications = $roadmapCourses->toBase()->map->shortLabel()->push($course->shortLabel())->unique()->values();
            $member = auth()->user();
        @endphp
        <section class="section crs-enroll {{ $tone() }}" id="enroll">
            <div class="wrap corp-plan-grid">
                <div class="corp-plan-copy">
                    <p class="kicker">{{ str_replace(':course', $courseMark, $enroll['kicker']) }}</p>
                    <h2>{{ $enroll['title'] }}</h2>
                    <p>{{ $enroll['lede'] }}</p>
                </div>
                <form class="corp-plan-card" method="post" action="{{ route('courses.enquiry', $course) }}">
                    @csrf
                    <p class="corp-plan-tag">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 3l7 3v5.5c0 4.2-2.9 7.9-7 9.5-4.1-1.6-7-5.3-7-9.5V6l7-3z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><path d="M9 12l2 2 4-4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        {{ $enroll['form_kicker'] }}
                    </p>
                    <h3>{{ $enroll['form_title'] }}</h3>
                    <p class="corp-plan-sub">{{ $enroll['form_lede'] }}</p>
                    <div class="corp-plan-fields">
                        <label class="corp-field">
                            <span>Full name <i>*</i></span>
                            <input class="input" name="name" required maxlength="120" autocomplete="name" value="{{ old('name', $member?->name) }}" @error('name') aria-invalid="true" @enderror>
                        </label>
                        <label class="corp-field">
                            <span>Work email <i>*</i></span>
                            <input class="input" type="email" name="email" required maxlength="255" autocomplete="email" value="{{ old('email', $member?->email) }}" @error('email') aria-invalid="true" @enderror>
                        </label>
                        <label class="corp-field">
                            <span>Phone <i>*</i></span>
                            <input class="input" type="tel" name="phone" required inputmode="numeric" pattern="[0-9]{7,15}" maxlength="15" title="Digits only, 7 to 15 digits" autocomplete="tel" data-digits value="{{ old('phone', preg_replace('/\D/', '', (string) $member?->phone)) }}" @error('phone') aria-invalid="true" @enderror>
                        </label>
                        <label class="corp-field">
                            <span>Certification <i>*</i></span>
                            <select class="select" name="certification" required @error('certification') aria-invalid="true" @enderror>
                                @foreach ($certifications as $certification)
                                    <option value="{{ $certification }}" @selected(old('certification', $course->shortLabel()) === $certification)>{{ $certification }}</option>
                                @endforeach
                            </select>
                        </label>
                        <label class="corp-field">
                            <span>Experience (years)</span>
                            <select class="select" name="experience" @error('experience') aria-invalid="true" @enderror>
                                <option value="">Select range</option>
                                @foreach ($enroll['experience'] as $experience)
                                    <option value="{{ $experience }}" @selected(old('experience') === $experience)>{{ $experience }}</option>
                                @endforeach
                            </select>
                        </label>
                        <label class="corp-field">
                            <span>Current role</span>
                            <input class="input" name="role" maxlength="120" autocomplete="organization-title" placeholder="{{ $enroll['role_placeholder'] }}" value="{{ old('role') }}" @error('role') aria-invalid="true" @enderror>
                        </label>
                        <label class="corp-field corp-field-wide">
                            <span>Anything you would like us to know?</span>
                            <textarea class="textarea" name="message" maxlength="5000" rows="3" placeholder="{{ $enroll['message_placeholder'] }}" @error('message') aria-invalid="true" @enderror>{{ old('message') }}</textarea>
                        </label>
                    </div>
                    <div class="corp-plan-foot">
                        <p>
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 3l7 3v5.5c0 4.2-2.9 7.9-7 9.5-4.1-1.6-7-5.3-7-9.5V6l7-3z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/></svg>
                            {{ $enroll['form_foot'] }}
                        </p>
                        <button class="btn btn-primary" type="submit">
                            {{ $enroll['submit'] }}
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </button>
                    </div>
                </form>
            </div>
        </section>
    @endif

    @if ($services->isNotEmpty())
        @include('public.partials.services', ['servicesTone' => $tone()])
    @endif

    @if ($roadmapCourses->isNotEmpty())
        @include('public.partials.roadmap', ['roadmapTone' => $tone(), 'roadmapId' => 'roadmap', 'currentCourse' => $course])
    @endif

    @if ($course->faqs->isNotEmpty())
        <section class="section crs-faq {{ $tone() }}" id="faqs">
            <div class="wrap crs-faq-grid">
                <div class="crs-faq-head">
                    <p class="kicker">{{ $course->shortLabel() }}® FAQs</p>
                    <h2>Your most asked questions.</h2>
                    <p>Have something else on your mind? <a href="{{ route('contact', ['message' => 'I have a question about '.$course->title.'.']) }}">Ask an advisor</a> or <a href="https://wa.me/{{ $site->get('whatsapp') }}">chat on WhatsApp</a>.</p>
                </div>
                <div class="crs-faq-list">
                    @foreach ($course->faqs as $faq)
                        <details>
                            <summary>
                                {{ $faq->question }}
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m6 9 6 6 6-6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </summary>
                            <p>{{ $faq->answer }}</p>
                        </details>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @include('components.public.cta-band', ['ctaTone' => $tone()])
@endsection
