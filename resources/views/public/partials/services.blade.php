<section class="section home-block {{ $servicesTone ?? 'band-plain' }}" id="services">
    <div class="wrap">
        <div class="home-svc-head">
            <p class="kicker">Our services</p>
            <h2>Everything you need on your PMI certification journey.</h2>
            <p class="lede">From application writing and self-paced learning to exam simulation, PDU renewal and one-to-one advisory.</p>
        </div>
        <div class="home-services">
            @foreach ($services as $service)
                @php $svcEntry = $service->entryPlan(); @endphp
                <article class="home-svc-card">
                    <div class="home-svc-top">
                        <span class="home-svc-icon">@include('components.public.service-icon', ['slug' => $service->slug])</span>
                        <p class="home-svc-price">
                            @if ($svcEntry)
                                <small>Starting at</small>
                                @if ($svcEntry->hasDiscount())
                                    <s>{{ $svcEntry->money($svcEntry->compare_price) }}</s>
                                @endif
                                <strong>{{ $svcEntry->money($svcEntry->price) }}</strong>
                            @else
                                <small>Pricing</small>
                                <strong>On request</strong>
                            @endif
                        </p>
                    </div>
                    <h3>{{ $service->title }}</h3>
                    <p class="home-svc-text">{{ $service->short_description }}</p>
                    @if ($service->plans->isNotEmpty())
                        <ul class="home-svc-tags">
                            @foreach ($service->plans->pluck('label')->unique() as $svcTag)
                                <li>{{ $svcTag }}</li>
                            @endforeach
                        </ul>
                    @endif
                    <div class="home-svc-foot">
                        @if ($service->plans->isNotEmpty())
                            <button class="home-svc-more" type="button" data-home-modal-open="svc-modal-{{ $service->id }}" aria-haspopup="dialog">
                                Learn more <span class="visually-hidden">about {{ $service->title }} pricing</span>
                                <span class="home-svc-go" aria-hidden="true"><svg width="14" height="14" viewBox="0 0 24 24" fill="none"><path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
                            </button>
                        @else
                            <a class="home-svc-more" href="{{ route('services.show', $service) }}">
                                Learn more <span class="visually-hidden">about {{ $service->title }}</span>
                                <span class="home-svc-go" aria-hidden="true"><svg width="14" height="14" viewBox="0 0 24 24" fill="none"><path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
                            </a>
                        @endif
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>

@php $boughtPlans = auth()->user()?->purchasedPlanIds() ?? ['enrolled' => [], 'pending' => []]; @endphp
@foreach ($services->filter(fn ($service) => $service->plans->isNotEmpty()) as $service)
    <div class="svc-modal" id="svc-modal-{{ $service->id }}" data-home-modal hidden>
        <button class="svc-modal-scrim" type="button" data-home-modal-close tabindex="-1" aria-label="Close"></button>
        <div class="svc-dialog" role="dialog" aria-modal="true" aria-labelledby="svc-modal-title-{{ $service->id }}">
            <header class="svc-dialog-head">
                <div>
                    <p class="kicker">Pricing</p>
                    <h2 id="svc-modal-title-{{ $service->id }}">{{ $service->title }}</h2>
                    <p>Select your certification track</p>
                </div>
                <button class="svc-dialog-x" type="button" data-home-modal-close aria-label="Close">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M6 6l12 12M18 6 6 18" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                </button>
            </header>
            <ul class="svc-tracks">
                @foreach ($service->plans as $plan)
                    <li class="svc-track">
                        <span class="svc-track-badge">{{ $plan->label }}</span>
                        <span class="svc-track-price">
                            @if ($plan->hasDiscount())
                                <s>{{ $plan->money($plan->compare_price) }}</s>
                            @endif
                            <strong>{{ $plan->money($plan->price) }}</strong>
                        </span>
                        <a class="svc-track-link" href="{{ route('services.show', $service) }}#packages">View details</a>
                        @if (in_array($plan->id, $boughtPlans['enrolled'], true))
                            <a class="svc-track-buy" href="{{ route('learn.services.show', $service) }}">Continue learning</a>
                        @elseif (isset($boughtPlans['pending'][$plan->id]))
                            <a class="svc-track-buy" href="{{ route('account.orders.show', $boughtPlans['pending'][$plan->id]) }}">View my order</a>
                        @else
                            <form method="post" action="{{ route('cart.plans.store', $plan->id) }}">
                                @csrf
                                <input type="hidden" name="buy_now" value="1">
                                <button class="svc-track-buy" type="submit">
                                    Buy Now <span class="visually-hidden">{{ $service->title }} for {{ $plan->label }}</span>
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </button>
                            </form>
                        @endif
                    </li>
                @endforeach
            </ul>
            <p class="svc-dialog-foot">Prices in {{ $pricing->currency() }} · Orders are confirmed after approval</p>
        </div>
    </div>
@endforeach
