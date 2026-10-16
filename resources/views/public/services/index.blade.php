@extends('layouts.public')

@push('head')
    <link href="{{ asset('assets/css/services.css') }}?v={{ @filemtime(public_path('assets/css/services.css')) }}" rel="stylesheet">
@endpush

@section('content')
    <section class="svc-hero svc-page-hero">
        <div class="wrap svc-intro">
            <p class="svc-pill"><span class="dot" aria-hidden="true"></span> {{ $countLabel }} premium services · PMI Authorized Training Partner</p>
            <h1>Our <span>premium services,</span> built for PMI candidates.</h1>
            <p>From writing a PMI application, to preparing for the exam, to renewing PDUs. Each service uses the programs and fees published by EdHut, with mentor review where the service includes it.</p>
        </div>
    </section>

    <section class="section svc-list">
        <div class="wrap svc-grid">
            @foreach ($services as $service)
                @php $plan = $service->plans->sortBy('price')->first(); @endphp
                <article class="svc-card">
                    <span class="svc-icon">@include('components.public.service-icon', ['slug' => $service->slug])</span>
                    <h2><a href="{{ route('services.show', $service) }}">{{ $service->title }}</a></h2>
                    <p>{{ $service->short_description }}</p>
                    @if ($service->plans->isNotEmpty())
                        <ul class="svc-fees">
                            @foreach ($service->plans->take(3) as $fee)
                                <li><span>{{ $fee->label }}</span> {{ $fee->priceLabel() }}</li>
                            @endforeach
                        </ul>
                    @endif
                    <div class="svc-card-foot">
                        <p>{{ $plan ? 'Starting at '.$plan->priceLabel() : 'Request quotation' }}</p>
                        <a class="svc-go" href="{{ route('services.show', $service) }}" aria-label="View {{ $service->title }}">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </a>
                    </div>
                </article>
            @endforeach
        </div>
    </section>

    @include('components.public.cta-band')
@endsection
