@extends('layouts.user')

@section('title', $order->number)

@section('content')
    @include('account.partials.top', ['heading' => $order->number])

    <section class="learn-card">
        <header class="card-head">
            <h2>{{ ucfirst($order->status) }}</h2>
            <span class="pill {{ $order->status === 'approved' ? 'is-ok' : ($order->status === 'rejected' ? 'is-bad' : 'is-wait') }}">{{ str_replace('_', ' ', $order->payment_status) }}</span>
        </header>
        @php
            $hasCourses = $order->items->contains(fn ($item) => $item->course_id !== null);
            $hasServices = $order->items->contains(fn ($item) => $item->service_plan_id !== null);
        @endphp
        @if ($order->status === 'pending')
            <p>Your order is placed. You can open the {{ $hasServices && ! $hasCourses ? 'service' : 'course' }} and continue only after an administrator approves it.</p>
        @endif
        <ul class="order-lines">
            @foreach ($order->items as $item)
                <li>
                    <strong>{{ $item->title }}</strong>
                    <span>{{ (float) $item->price > 0 ? $order->money($item->price) : 'Request quotation' }}</span>
                </li>
            @endforeach
        </ul>
        @if ($order->admin_note)
            <p class="muted">{{ $order->admin_note }}</p>
        @endif
        @if ($order->status === 'approved')
            @if ($hasCourses || ! $hasServices)
                <a class="btn btn-primary" href="{{ route('account.courses.index') }}">Go to my courses</a>
            @endif
            @if ($hasServices)
                <a class="btn {{ $hasCourses ? 'btn-line' : 'btn-primary' }}" href="{{ route('account.services.index') }}">Go to my services</a>
            @endif
        @endif
    </section>
@endsection
