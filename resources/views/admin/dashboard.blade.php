@extends('layouts.admin')

@section('content')
    @php
        $first = strtok(auth()->user()->name, ' ') ?: auth()->user()->name;
        $bars = [
            ['New enquiries', $counts['enquiries'], $counts['enquiry_total'], 'ok'],
            ['Pending orders', $counts['pending_orders'], $counts['orders'], 'wait'],
            ['Active courses', $counts['active_courses'], $counts['courses'], 'info'],
        ];
    @endphp
    <section class="dash-top">
        <article class="welcome-card">
            <p class="hello">{{ $greeting }}</p>
            <h1>Welcome back, {{ $first }}!</h1>
            <p>Here's what's happening on the site today.</p>
            <div class="hero-actions">
                <a class="btn btn-blue" href="{{ route('admin.modules.index', 'courses') }}">Manage courses</a>
                <a class="btn btn-soft" href="{{ route('admin.enquiries.index') }}">View enquiries</a>
            </div>
        </article>
        <article class="panel quick-card">
            <h2>Quick actions</h2>
            <div class="quick-grid">
                <a href="{{ route('admin.modules.index', 'courses') }}"><span class="q-icon q-violet">@include('components.admin.icon', ['name' => 'book'])</span> Courses</a>
                <a href="{{ route('admin.modules.index', 'services') }}"><span class="q-icon q-blue">@include('components.admin.icon', ['name' => 'grid'])</span> Services</a>
                <a href="{{ route('admin.enquiries.index') }}"><span class="q-icon q-rose">@include('components.admin.icon', ['name' => 'mail'])</span> Enquiries</a>
                <a href="{{ route('admin.orders.index') }}"><span class="q-icon q-green">@include('components.admin.icon', ['name' => 'cart'])</span> Orders</a>
            </div>
        </article>
        <article class="panel summary-card">
            <div class="summary-head">
                <h2>Today's summary</h2>
                <time datetime="{{ now()->toDateString() }}">{{ now()->format('M j') }}</time>
            </div>
            @foreach ($bars as [$label, $value, $total, $tone])
                @php $step = $total > 0 ? (int) (round((($value / $total) * 100) / 10) * 10) : 0; @endphp
                <div class="sum-row">
                    <span><i class="dot dot-{{ $tone }}"></i> {{ $label }}</span>
                    <strong>{{ $value }}@if($total > 0) / {{ $total }}@endif</strong>
                    <span class="bar"><span class="bar-{{ $tone }} w-{{ $step }}"></span></span>
                </div>
            @endforeach
        </article>
    </section>

    <section class="metric-grid">
        <article class="panel metric">
            <p>Total courses</p>
            <strong>{{ $counts['courses'] }}</strong>
            <span>Active catalog items</span>
            <svg class="spark spark-violet" viewBox="0 0 240 36" aria-hidden="true"><path d="M0 24 C 30 24, 40 8, 70 14 S 120 30, 150 16 200 8, 240 18" fill="none" stroke="currentColor" stroke-width="2.4"/></svg>
        </article>
        <article class="panel metric">
            <p>Total students</p>
            <strong>{{ $counts['students'] }}</strong>
            <span>Learner accounts</span>
            <svg class="spark spark-green" viewBox="0 0 240 36" aria-hidden="true"><path d="M0 20 C 40 28, 60 10, 100 16 S 160 26, 200 12 220 18, 240 14" fill="none" stroke="currentColor" stroke-width="2.4"/></svg>
        </article>
        <article class="panel metric">
            <p>Total orders</p>
            <strong>{{ $counts['orders'] }}</strong>
            <span>{{ $counts['pending_orders'] }} waiting for review</span>
            <svg class="spark spark-amber" viewBox="0 0 240 36" aria-hidden="true"><path d="M0 16 C 35 16, 50 28, 90 22 S 150 8, 190 18 220 24, 240 12" fill="none" stroke="currentColor" stroke-width="2.4"/></svg>
        </article>
    </section>
@endsection
