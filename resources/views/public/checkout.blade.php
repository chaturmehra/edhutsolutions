@extends('layouts.public')

@push('head')
    <link href="{{ asset('assets/css/services.css') }}?v={{ @filemtime(public_path('assets/css/services.css')) }}" rel="stylesheet">
    <link href="{{ asset('assets/css/courses.css') }}?v={{ @filemtime(public_path('assets/css/courses.css')) }}" rel="stylesheet">
@endpush

@php
    $user = auth()->user();
    $fee = fn ($amount) => (float) $amount > 0 ? $pricing->format($amount) : 'Request quotation';
    $totalLabel = $total > 0 ? $pricing->formatConverted($total) : 'Request quotation';
@endphp

@section('content')
    <section class="section checkout-page">
        <div class="wrap">
            <p class="secure-line">Secure Checkout</p>
            <div class="checkout-grid">
                <form class="checkout-card" method="post" action="{{ route('checkout.store') }}">
                    @csrf
                    <h2>Your Details</h2>
                    <p class="checkout-note">Logged in as {{ $user->email }}. Your order will be linked to your account.</p>
                    <label class="field">
                        <span>Full name</span>
                        <input class="input" type="text" name="name" value="{{ old('name', $user->name) }}" required>
                    </label>
                    <label class="field">
                        <span>Email address</span>
                        <input class="input" type="email" value="{{ $user->email }}" readonly>
                    </label>
                    <label class="field">
                        <span>Phone number</span>
                        <input class="input" type="tel" name="phone" value="{{ old('phone', $user->phone) }}" required>
                    </label>
                    <label class="field">
                        <span>Country</span>
                        <select class="select" name="country" autocomplete="country" required>
                            <option value="">Select your country</option>
                            @foreach ($countries as $country)
                                <option value="{{ $country->code }}" @selected($selectedCountry === $country->code)>{{ $country->name }}</option>
                            @endforeach
                        </select>
                    </label>
                    @if ($errors->any())
                        <p class="checkout-note">{{ $errors->first() }}</p>
                    @endif
                    <button class="btn btn-primary" type="submit">Order Placed</button>
                </form>
                <aside class="checkout-card checkout-summary">
                    <p class="protect-line">Your order is protected · Approval-assured</p>
                    <h2>Order Summary</h2>
                    @foreach ($items as $item)
                        <article class="checkout-item">
                            <span class="drawer-badge">{{ $item->badge() }}</span>
                            <div>
                                <strong>{{ $item->label() }}</strong>
                                <span>{{ $fee($item->price) }}</span>
                            </div>
                        </article>
                    @endforeach
                    <div class="checkout-fees">
                        <p><span>Program fees</span><strong>{{ $totalLabel }}</strong></p>
                        <p class="drawer-total"><span>Grand Total</span><strong>{{ $totalLabel }}</strong></p>
                    </div>
                </aside>
            </div>
        </div>
    </section>
@endsection
