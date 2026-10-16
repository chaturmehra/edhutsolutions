@extends('layouts.user')

@section('title', $service->title)

@section('content')
    @include('account.partials.top', ['heading' => $service->title])

    @php
        $tabs = array_filter([
            'overview' => 'Overview',
            'study-material' => 'Study Material',
            'video' => $service->videos->isNotEmpty() ? 'Video' : null,
        ]);
    @endphp

    <section class="learn-card learn-tabs" data-tabs="hash">
        <div class="learn-tab-list" role="tablist" aria-label="{{ $service->title }}">
            @foreach ($tabs as $key => $label)
                <button class="learn-tab" type="button" role="tab" id="learn-tab-{{ $key }}" aria-controls="{{ $key }}" aria-selected="{{ $loop->first ? 'true' : 'false' }}" tabindex="{{ $loop->first ? '0' : '-1' }}">{{ $label }}</button>
            @endforeach
        </div>

        <div class="learn-tab-panel" role="tabpanel" id="overview" aria-labelledby="learn-tab-overview">
            @if (filled($service->short_description))
                <p>{{ $service->short_description }}</p>
            @endif
            <dl class="learn-facts">
                @if ($enrollment?->plan)
                    <div><dt>Track</dt><dd>{{ $enrollment->plan->label }}</dd></div>
                @endif
                @if ($enrollment?->order)
                    <div><dt>Order</dt><dd><a href="{{ route('account.orders.show', $enrollment->order) }}">{{ $enrollment->order->number }}</a></dd></div>
                @endif
                @if ($enrollment?->enrolled_at)
                    <div><dt>Approved on</dt><dd>{{ $enrollment->enrolled_at->format('M j, Y') }}</dd></div>
                @endif
                <div><dt>Study material</dt><dd>{{ $service->pdfs->count() }} {{ $service->pdfs->count() === 1 ? 'PDF' : 'PDFs' }} · {{ $service->videos->count() }} {{ \Illuminate\Support\Str::plural('video', $service->videos->count()) }}</dd></div>
            </dl>
            <p class="learn-note muted">Files and videos added by EdHut for this service appear in the tabs above. <a href="{{ route('services.show', $service) }}">View the service page</a></p>
        </div>

        <div class="learn-tab-panel" role="tabpanel" id="study-material" aria-labelledby="learn-tab-study-material" hidden>
            @include('account.courses.partials.pdf-table', ['pdfs' => $service->pdfs, 'empty' => 'No study material has been published for this service yet.'])
        </div>

        @isset($tabs['video'])
            <div class="learn-tab-panel" role="tabpanel" id="video" aria-labelledby="learn-tab-video" hidden>
                @include('account.courses.partials.video-table', ['videos' => $service->videos])
            </div>
        @endisset
    </section>

    @isset($tabs['video'])
        @include('account.courses.partials.video-dialog', ['frameTitle' => 'Service video'])
    @endisset
@endsection
