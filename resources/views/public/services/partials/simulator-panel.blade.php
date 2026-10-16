@php
    $simPlanLabel = $heroPlan ? $heroPlan->label.'®' : null;
@endphp
<div class="sim-panel">
    <div class="sim-head">
        <div class="sim-head-copy">
            <p class="sim-kicker"><span class="sim-kicker-icon" aria-hidden="true"><svg width="16" height="16" viewBox="0 0 24 24" fill="none">@include('components.public.icon', ['name' => 'clipboard'])</svg></span> {{ $simulator['kicker'] }}</p>
            <p class="sim-title">
                @if ($simPlanLabel)
                    <strong>{{ $simPlanLabel }}</strong>
                @endif
                <span>{{ $service->title }}</span>
            </p>
            <p class="sim-badge"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" aria-hidden="true">@include('components.public.icon', ['name' => 'shield'])</svg> {{ $simulator['badge'] }}</p>
            <p class="sim-tagline">{{ $simulator['tagline'] }}</p>
        </div>
        <p class="sim-ring"><strong>{{ $simulator['ring']['value'] }}</strong><span>{{ $simulator['ring']['label'] }}</span></p>
    </div>
    <div class="sim-body">
        <ul class="sim-stats">
            @foreach ($simulator['stats'] as $stat)
                <li>
                    <span class="sim-stat-icon" aria-hidden="true"><svg width="20" height="20" viewBox="0 0 24 24" fill="none">@include('components.public.icon', ['name' => $stat['icon']])</svg></span>
                    <strong>{{ $stat['title'] }}</strong>
                    <span>{{ $stat['text'] }}</span>
                </li>
            @endforeach
        </ul>
        <div class="sim-mock" aria-hidden="true">
            <div class="sim-mock-head">
                <strong>{{ $simPlanLabel ? str_replace(':plan®', $simPlanLabel, $simulator['question']['title']) : $service->title }}</strong>
                <span>{{ $simulator['question']['meta'] }}</span>
            </div>
            <span class="sim-line"></span>
            <span class="sim-line sim-line-short"></span>
            <ol class="sim-options">
                @foreach (['A', 'B', 'C', 'D'] as $option)
                    <li @class(['is-picked' => $loop->index === 1])><b>{{ $option }}</b><span class="sim-line"></span></li>
                @endforeach
            </ol>
            <div class="sim-rationale">
                <span class="sim-rationale-tag"><svg width="13" height="13" viewBox="0 0 24 24" fill="none">@include('components.public.icon', ['name' => 'bulb'])</svg> {{ $simulator['question']['rationale'] }}</span>
                <span class="sim-line"></span>
                <span class="sim-line sim-line-short"></span>
            </div>
            <div class="sim-mock-foot">
                <span>&larr; {{ $simulator['question']['previous'] }}</span>
                <span class="sim-next">{{ $simulator['question']['next'] }} &rarr;</span>
            </div>
        </div>
        <div class="sim-side">
            @if ($service->plans->isNotEmpty())
                <div class="sim-card">
                    <p class="sim-card-title">{{ $simulator['plans_title'] }}</p>
                    <ul class="sim-plans">
                        @foreach ($service->plans as $plan)
                            <li><a href="{{ route('services.show', $service) }}?plan={{ urlencode($plan->label) }}" @if ($heroPlan && $plan->is($heroPlan)) aria-current="true" @endif>{{ $plan->label }}®</a></li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @if ($heroPlan)
                <div class="sim-card sim-package">
                    <p class="sim-card-title">{{ $simulator['package_title'] }}</p>
                    <p class="sim-package-plan">{{ $simPlanLabel }}</p>
                    <p class="sim-package-price"><small>{{ $pricing->currency() }}</small> {{ $pricing->number($heroPlan->price) }}</p>
                    @if ($heroPlan->hasDiscount())
                        <p class="svc-was"><s>{{ $heroPlan->money($heroPlan->compare_price) }}</s> <em>{{ $heroPlan->discountPercent() }}% OFF</em></p>
                    @endif
                    @if (filled($heroPlan->description))
                        <p class="sim-package-access">{{ $simulator['access_label'] }}: {{ $heroPlan->description }}</p>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
