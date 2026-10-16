@extends('layouts.user')

@section('title', 'My services')

@section('content')
    @include('account.partials.top', ['heading' => 'My services'])

    <section class="learn-card">
        <header class="card-head">
            <h2>Enrolled services</h2>
            <div class="chip-row">
                <a class="{{ $filter === 'all' ? 'is-on' : '' }}" href="{{ route('account.services.index') }}">All ({{ $counts['all'] }})</a>
                <a class="{{ $filter === 'pending' ? 'is-on' : '' }}" href="{{ route('account.services.index', ['status' => 'pending']) }}">Waiting ({{ $counts['pending'] }})</a>
                <a class="{{ $filter === 'approved' ? 'is-on' : '' }}" href="{{ route('account.services.index', ['status' => 'approved']) }}">Approved ({{ $counts['approved'] }})</a>
            </div>
        </header>

        <div class="enroll-list">
            @foreach ($enrollments as $enrollment)
                @php($service = $enrollment->service)
                <article class="enroll-card">
                    <div class="enroll-media">
                        @include('account.partials.thumb', ['course' => $service])
                    </div>
                    <div>
                        <span class="pill {{ $enrollment->status === 'active' ? 'is-ok' : 'is-wait' }}">{{ $enrollment->status === 'active' ? 'Approved' : ucfirst($enrollment->status) }}</span>
                        <h3>{{ $service->title }}</h3>
                        <p class="meta-row">
                            @if ($enrollment->plan)
                                <span>{{ $enrollment->plan->label }}</span>
                            @endif
                            <span>{{ $service->pdfs_count }} {{ $service->pdfs_count === 1 ? 'PDF' : 'PDFs' }}</span>
                            <span>{{ $service->videos_count }} {{ \Illuminate\Support\Str::plural('video', $service->videos_count) }}</span>
                        </p>
                        @if ($enrollment->status === 'active' && $service->isActive())
                            <a class="btn btn-primary" href="{{ route('learn.services.show', $service) }}">Open service</a>
                        @else
                            <p class="muted">This service is not open.</p>
                        @endif
                    </div>
                </article>
            @endforeach

            @foreach ($pending as $order)
                @foreach ($order->items as $item)
                    <article class="enroll-card">
                        <div class="enroll-media">
                            @include('account.partials.thumb', ['course' => $item->servicePlan?->service])
                        </div>
                        <div>
                            <span class="pill is-wait">Waiting for approval</span>
                            <h3>{{ $item->title }}</h3>
                            <p class="muted">Order {{ $order->number }}. You can open the service after an administrator approves it.</p>
                            <a class="btn btn-line" href="{{ route('account.orders.show', $order) }}">View order</a>
                        </div>
                    </article>
                @endforeach
            @endforeach

            @if ($enrollments->isEmpty() && $pending->isEmpty())
                <p class="muted">No services in this list yet. <a href="{{ route('services.index') }}">Browse services</a></p>
            @endif
        </div>
    </section>
@endsection
