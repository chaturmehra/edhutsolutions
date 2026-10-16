@extends('layouts.public')

@push('head')
    <link href="{{ asset('assets/css/showcase.css') }}?v={{ @filemtime(public_path('assets/css/showcase.css')) }}" rel="stylesheet">
    <link href="{{ asset('assets/css/services.css') }}?v={{ @filemtime(public_path('assets/css/services.css')) }}" rel="stylesheet">
@endpush

@section('content')
    @php
        $enquiry = route('contact', ['message' => 'I would like to enquire about '.$service->title.'.']);
        $wantedPlan = request()->query('plan');
        $heroPlan = $service->plans->first(fn ($plan) => is_string($wantedPlan) && strcasecmp($plan->label, trim($wantedPlan)) === 0)
            ?? $service->plans->first();
        $simulator = \App\Support\Content::get('services.simulator')[$service->slug] ?? null;
    @endphp
    <section @class(['svc-hero', 'svc-page-hero', 'svc-detail-hero', 'has-world-map', 'svc-sim-hero' => $simulator])>
        <div class="wrap svc-detail-grid">
            <div>
                <p class="svc-crumb"><a href="{{ route('services.index') }}">Services</a> <span aria-hidden="true">/</span> {{ $service->title }}</p>
                <p class="svc-pill"><span class="dot" aria-hidden="true"></span> PMI Authorized Training Partner</p>
                <h1>{{ $hero['headline'] }}</h1>
                <p>{{ $service->short_description }}</p>
                <div class="hero-actions">
                    <a class="btn btn-primary" href="{{ $enquiry }}">Request this service <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg></a>
                    @if ($service->plans->isNotEmpty())
                        <a class="btn btn-ghost" href="#packages">View packages</a>
                    @endif
                </div>
                @if ($simulator)
                    <ul class="svc-hero-features">
                        @foreach ($simulator['features'] as $feature)
                            <li>
                                <span class="svc-hero-feature-icon" aria-hidden="true"><svg width="18" height="18" viewBox="0 0 24 24" fill="none">@include('components.public.icon', ['name' => $feature['icon']])</svg></span>
                                <strong>{{ $feature['title'] }}</strong>
                                <span>{{ $feature['text'] }}</span>
                            </li>
                        @endforeach
                    </ul>
                @endif
                @php
                    $heroStats = \App\Support\Content::get('services.hero_stats')[$service->slug] ?? [];
                @endphp
                @if ($heroStats)
                    <ul class="svc-hero-stats">
                        @foreach ($heroStats as $stat)
                            <li>
                                <span class="svc-hero-stat-icon" aria-hidden="true"><svg width="20" height="20" viewBox="0 0 24 24" fill="none">@include('components.public.icon', ['name' => $stat['icon']])</svg></span>
                                <strong>{{ $stat['value'] }}</strong>
                                <span>{{ $stat['label'] }}</span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
            @php
                $heroCard = \App\Support\Content::get('services.hero_card')[$service->slug] ?? null;
                $journey = \App\Support\Content::get('services.journey')[$service->slug] ?? null;
            @endphp
            @if ($simulator)
                @include('public.services.partials.simulator-panel')
            @elseif ($heroCard)
                @include('public.services.partials.talent-card')
            @elseif ($journey)
                @include('public.services.partials.journey-orbit')
            @else
            @php
                $nodeIcons = [
                    '<path d="M4 17l6-6 4 4 6-7M14 8h6v6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>',
                    '<path d="M12 3l7 3v5c0 4.5-3 8.3-7 10-4-1.7-7-5.5-7-10V6l7-3z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>',
                    '<path d="M16 19v-1.5a3.5 3.5 0 00-3.5-3.5h-5A3.5 3.5 0 004 17.5V19M10 10.5a3.5 3.5 0 100-7 3.5 3.5 0 000 7zM20 19v-1.5a3.5 3.5 0 00-2.5-3.35M15.5 3.65a3.5 3.5 0 010 6.7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>',
                    '<circle cx="12" cy="12" r="8.5" stroke="currentColor" stroke-width="1.8"/><circle cx="12" cy="12" r="5" stroke="currentColor" stroke-width="1.8"/><circle cx="12" cy="12" r="1.6" fill="currentColor"/>',
                ];
            @endphp
            <div class="svc-sphere">
                <span class="svc-sphere-glow" aria-hidden="true"></span>
                <div class="svc-sphere-track">
                    @foreach (array_slice($hero['nodes'], 0, 4) as $node)
                        <div class="svc-node svc-node-{{ $loop->iteration }}">
                            <div class="svc-node-spin">
                                <div class="svc-node-body">
                                    <span class="svc-node-icon" aria-hidden="true"><svg width="22" height="22" viewBox="0 0 24 24" fill="none">{!! $nodeIcons[$loop->index] !!}</svg></span>
                                    <span class="svc-node-label">{{ $node }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                @php
                    $sphereText = \App\Support\Content::get('services.sphere')[$service->slug] ?? null;
                    if ($sphereText && str_contains($sphereText['title'], ':plan')) {
                        $sphereText = $heroPlan ? array_merge($sphereText, ['title' => str_replace(':plan', $heroPlan->label, $sphereText['title'])]) : null;
                    }
                @endphp
                @if ($sphereText)
                    <div class="svc-sphere-core svc-sphere-core-title">
                        <strong>{{ $sphereText['title'] }}</strong>
                        <span class="svc-sphere-subtitle">{{ $sphereText['subtitle'] }}</span>
                        <span class="svc-sphere-rule" aria-hidden="true"></span>
                        <span class="svc-sphere-tagline">{{ $sphereText['tagline'] }}</span>
                    </div>
                @else
                    <div class="svc-sphere-core">
                        <span class="svc-sphere-kicker">Join the elite<br>trusted by</span>
                        <strong>{{ $site->get('stat_students') }}</strong>
                        <span class="svc-sphere-label">Professionals Trained Globally</span>
                        <span class="svc-sphere-stars" aria-label="Rated 5 out of 5">★★★★★</span>
                    </div>
                @endif
            </div>
            @endif
        </div>
        @php
            $heroStrip = \App\Support\Content::get('services.hero_strip')[$service->slug] ?? [];
        @endphp
        @if ($heroStrip)
            <div class="wrap">
                <ul class="svc-hero-strip">
                    @foreach ($heroStrip as $stripItem)
                        <li>
                            <span class="svc-hero-strip-icon" aria-hidden="true"><svg width="18" height="18" viewBox="0 0 24 24" fill="none">@include('components.public.icon', ['name' => $stripItem['icon']])</svg></span>
                            <span class="svc-hero-strip-copy">
                                <strong>{{ $stripItem['title'] }}</strong>
                                <span>{{ $stripItem['text'] }}</span>
                            </span>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
    </section>

    @php
        $why = \App\Support\Content::get('services.why');
        $whyService = $why['services'][$service->slug] ?? [
            'lede' => $service->short_description,
            'items' => array_map(fn ($point) => ['icon' => 'check', 'title' => null, 'text' => $point], $points),
        ];
    @endphp
    <section class="section band-tint svc-why">
        <div class="wrap">
            <div class="svc-why-head">
                <p class="kicker">{{ $why['kicker'] }}</p>
                <h2>{{ str_replace(':service', $service->title, $why['title']) }}</h2>
                @if (filled($whyService['lede']))
                    <p>{{ $whyService['lede'] }}</p>
                @endif
            </div>
            <div class="svc-why-grid" data-shot-track data-shot-label="Show reason">
                @foreach ($whyService['items'] as $whyItem)
                    <article class="svc-why-card">
                        <span class="svc-why-icon" aria-hidden="true"><svg width="18" height="18" viewBox="0 0 24 24" fill="none">@include('components.public.icon', ['name' => $whyItem['icon'] ?? 'check'])</svg></span>
                        @if (filled($whyItem['title'] ?? null))
                            <h3>{{ $whyItem['title'] }}</h3>
                        @endif
                        <p>{{ $whyItem['text'] }}</p>
                    </article>
                @endforeach
            </div>
            <div class="shot-dots" data-shot-dots></div>
        </div>
    </section>

    @php
        $pricingCopy = \App\Support\Content::get('services.pricing');
        $pricingCards = in_array($service->slug, $pricingCopy['card_layout'] ?? [], true);
        $boughtPlans = auth()->user()?->purchasedPlanIds() ?? ['enrolled' => [], 'pending' => []];
        $pricingTiles = collect($whyService['items'])->filter(fn ($item) => filled($item['title'] ?? null))->take(4);
        $buyArrow = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>';
    @endphp
    <section class="section band-plain svc-pricing" id="packages">
        <div class="wrap">
            <div class="svc-why-head">
                <p class="kicker">{{ $pricingCopy['kicker'] }}</p>
                <h2>{{ $pricingCopy['title'] }}</h2>
                <p>{{ str_replace(':service', $service->title, $service->plans->count() === 1 ? $pricingCopy['lede_single'] : $pricingCopy['lede']) }}</p>
            </div>
            @if ($service->plans->isEmpty())
                <a class="btn btn-primary" href="{{ $enquiry }}">{{ $pricingCopy['empty'] }}</a>
            @elseif ($pricingCards)
                <div @class(['svc-tier-grid', 'svc-tier-grid-'.min($service->plans->count(), 3)])>
                    @foreach ($service->plans as $plan)
                        <article class="svc-tier svc-tier-tone-{{ ($loop->index % 3) + 1 }}">
                            <span class="svc-tier-medal" aria-hidden="true"><svg width="22" height="22" viewBox="0 0 24 24" fill="none">@include('components.public.icon', ['name' => 'shield'])</svg></span>
                            <h3>{{ $plan->label }}</h3>
                            <div class="svc-tier-rule">
                                @if (filled($plan->description))
                                    <span>{{ $plan->description }}</span>
                                @endif
                            </div>
                            <p class="svc-tier-price"><small>{{ $pricing->currency() }}</small> {{ $pricing->number($plan->price) }}</p>
                            @if ($plan->hasDiscount())
                                <p class="svc-was"><s>{{ $plan->money($plan->compare_price) }}</s> <em>{{ $plan->discountPercent() }}% OFF</em></p>
                            @endif
                            @if (in_array($plan->id, $boughtPlans['enrolled'], true))
                                <div class="svc-tier-cta"><a class="svc-tier-buy" href="{{ route('learn.services.show', $service) }}">Continue learning{!! $buyArrow !!}</a></div>
                            @elseif (isset($boughtPlans['pending'][$plan->id]))
                                <div class="svc-tier-cta"><a class="svc-tier-buy" href="{{ route('account.orders.show', $boughtPlans['pending'][$plan->id]) }}">View my order{!! $buyArrow !!}</a></div>
                            @else
                                <form method="post" action="{{ route('cart.plans.store', $plan->id) }}">
                                    @csrf
                                    <input type="hidden" name="buy_now" value="1">
                                    <button class="svc-tier-buy" type="submit">{{ $pricingCopy['buy'] }} <span class="visually-hidden">{{ $service->title }} for {{ $plan->label }}</span>{!! $buyArrow !!}</button>
                                </form>
                            @endif
                        </article>
                    @endforeach
                </div>
            @else
                <div class="svc-pack" data-plan-tabs>
                    @if ($service->plans->count() > 1)
                        <div class="svc-tabs" role="tablist" aria-label="{{ $pricingCopy['tabs_label'] }}">
                            @foreach ($service->plans as $plan)
                                <button type="button" role="tab" data-plan="{{ $plan->id }}" data-plan-label="{{ $plan->label }}" aria-selected="{{ $loop->first ? 'true' : 'false' }}" @class(['is-selected' => $loop->first])>{{ $plan->label }}</button>
                            @endforeach
                        </div>
                    @endif
                    @foreach ($service->plans as $plan)
                        <div class="svc-pack-card" data-plan-panel="{{ $plan->id }}" @unless($loop->first) hidden @endunless>
                            <div class="svc-pack-info">
                                <p class="svc-pack-label">{{ $plan->label }}</p>
                                <h3>{{ $plan->label }} {{ $service->title }}</h3>
                                <p>{{ filled($plan->description) ? $plan->description : $service->short_description }}</p>
                                @if ($pricingTiles->isNotEmpty())
                                    <ul class="svc-pack-tiles">
                                        @foreach ($pricingTiles as $tile)
                                            <li>
                                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true">@include('components.public.icon', ['name' => $tile['icon'] ?? 'check'])</svg>
                                                {{ $tile['title'] }}
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </div>
                            <div class="svc-pack-buy">
                                <p class="svc-pack-fee">{{ $pricingCopy['fee_label'] }}</p>
                                <p class="svc-pack-price"><small>{{ $pricing->currency() }}</small> {{ $pricing->number($plan->price) }}</p>
                                @if ($plan->hasDiscount())
                                    <p class="svc-was"><s>{{ $plan->money($plan->compare_price) }}</s> <em>{{ $plan->discountPercent() }}% OFF</em></p>
                                @endif
                                @if (in_array($plan->id, $boughtPlans['enrolled'], true))
                                    <a class="btn svc-pack-btn" href="{{ route('learn.services.show', $service) }}">Continue learning{!! $buyArrow !!}</a>
                                @elseif (isset($boughtPlans['pending'][$plan->id]))
                                    <a class="btn svc-pack-btn" href="{{ route('account.orders.show', $boughtPlans['pending'][$plan->id]) }}">View my order{!! $buyArrow !!}</a>
                                @else
                                    <form method="post" action="{{ route('cart.plans.store', $plan->id) }}">
                                        @csrf
                                        <input type="hidden" name="buy_now" value="1">
                                        <button class="btn svc-pack-btn" type="submit">{{ $pricingCopy['buy'] }} <span class="visually-hidden">{{ $service->title }} for {{ $plan->label }}</span>{!! $buyArrow !!}</button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
            @if ($service->plans->isNotEmpty())
                <p class="svc-pricing-foot">{{ $pricingCopy['foot'] }} <a href="{{ $enquiry }}">{{ $pricingCopy['help'] }}</a></p>
            @endif
        </div>
    </section>

    @if ($others->isNotEmpty())
        @php
            $more = \App\Support\Content::get('services.more');
        @endphp
        <section class="section svc-more-band">
            <div class="wrap">
                <div class="svc-why-head">
                    <p class="kicker">{{ $more['kicker'] }}</p>
                    <h2>{{ $more['title'] }}</h2>
                    <p>{{ $more['lede'] }}</p>
                </div>
                <div class="svc-more">
                    @foreach ($others as $other)
                        <a class="svc-more-card" href="{{ route('services.show', $other) }}">
                            <span class="svc-more-icon" aria-hidden="true">@include('components.public.service-icon', ['slug' => $other->slug])</span>
                            <strong>{{ $other->title }}</strong>
                            <span class="svc-more-text">{{ $other->short_description }}</span>
                            <span class="svc-more-link">{{ $more['link'] }} <svg width="14" height="14" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @include('components.public.cta-band', ['ctaTone' => $others->isNotEmpty() ? 'band-plain' : 'band-tint'])
@endsection
