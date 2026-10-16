@extends('layouts.user')

@section('title', 'My courses')

@section('content')
    @include('account.partials.top', ['heading' => 'My courses'])

    <section class="learn-card">
        <header class="card-head">
            <h2>Enrolled courses</h2>
            <div class="chip-row">
                <a class="{{ $filter === 'all' ? 'is-on' : '' }}" href="{{ route('account.courses.index') }}">All ({{ $counts['all'] }})</a>
                <a class="{{ $filter === 'pending' ? 'is-on' : '' }}" href="{{ route('account.courses.index', ['status' => 'pending']) }}">Waiting ({{ $counts['pending'] }})</a>
                <a class="{{ $filter === 'approved' ? 'is-on' : '' }}" href="{{ route('account.courses.index', ['status' => 'approved']) }}">Approved ({{ $counts['approved'] }})</a>
            </div>
        </header>

        <div class="enroll-list">
            @foreach ($enrollments as $enrollment)
                @if ($enrollment->course)
                    <article class="enroll-card">
                        <div class="enroll-media">
                            @include('account.partials.thumb', ['course' => $enrollment->course])
                        </div>
                        <div>
                            <span class="pill {{ $enrollment->status === 'active' ? 'is-ok' : 'is-wait' }}">{{ $enrollment->status === 'active' ? 'Approved' : ucfirst($enrollment->status) }}</span>
                            <h3>{{ $enrollment->course->title }}</h3>
                            <p class="meta-row">
                                @if ($enrollment->course->lessons_count)
                                    <span>{{ $enrollment->course->lessons_count }} {{ \Illuminate\Support\Str::plural('lesson', $enrollment->course->lessons_count) }}</span>
                                @endif
                                @if ($enrollment->course->duration)
                                    <span>{{ $enrollment->course->duration }}</span>
                                @endif
                            </p>
                            @if ($enrollment->status === 'active')
                                <a class="btn btn-primary" href="{{ route('learn.show', $enrollment->course) }}">Start learning</a>
                            @else
                                <p class="muted">This enrollment is not open.</p>
                            @endif
                        </div>
                    </article>
                @endif
            @endforeach

            @foreach ($pending as $order)
                @foreach ($order->items as $item)
                    <article class="enroll-card">
                        <div class="enroll-media">
                            @include('account.partials.thumb', ['course' => $item->course])
                        </div>
                        <div>
                            <span class="pill is-wait">Waiting for approval</span>
                            <h3>{{ $item->title }}</h3>
                            <p class="muted">Order {{ $order->number }}. You can open the course after an administrator approves it.</p>
                            <a class="btn btn-line" href="{{ route('account.orders.show', $order) }}">View order</a>
                        </div>
                    </article>
                @endforeach
            @endforeach

            @if ($enrollments->isEmpty() && $pending->isEmpty())
                <p class="muted">No courses in this list yet.</p>
            @endif
        </div>
    </section>
@endsection
