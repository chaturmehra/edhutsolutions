@extends('layouts.public')

@push('head')
    <link href="{{ asset('assets/css/services.css') }}?v={{ @filemtime(public_path('assets/css/services.css')) }}" rel="stylesheet">
    <link href="{{ asset('assets/css/courses.css') }}?v={{ @filemtime(public_path('assets/css/courses.css')) }}" rel="stylesheet">
@endpush

@section('content')
    <section class="svc-hero">
        <div class="wrap svc-intro">
            <p class="svc-pill"><span class="dot" aria-hidden="true"></span> Cart</p>
            <h1>Your <span>cart.</span></h1>
            <p>A seat request stays pending until an administrator approves it. Course files open after that approval.</p>
        </div>
    </section>
    <section class="section">
        <div class="wrap">
            @if ($items->isEmpty())
                <div class="checkout-card">
                    <p>Your cart is empty.</p>
                    <a class="btn btn-primary" href="{{ route('courses.index') }}">Browse courses</a>
                </div>
            @else
                <table class="cart-table">
                    <thead>
                        <tr><th>Item</th><th>Fee</th><th></th></tr>
                    </thead>
                    <tbody>
                        @foreach ($items as $item)
                            <tr>
                                <td><a href="{{ $item->link() }}">{{ $item->label() }}</a></td>
                                <td>{{ (float) $item->price > 0 ? $pricing->label($item->price) : 'Request quotation' }}</td>
                                <td>
                                    <form method="post" action="{{ route('cart.destroy', $item) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button class="cart-remove" type="submit">Remove</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="hero-actions">
                    <a class="btn btn-primary" href="{{ route('checkout.create') }}">Checkout</a>
                    <a class="btn btn-ghost" href="{{ route('courses.index') }}">Continue browsing</a>
                </div>
            @endif
        </div>
    </section>
@endsection
