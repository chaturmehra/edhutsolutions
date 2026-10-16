@extends('layouts.user')

@section('title', 'My orders')

@section('content')
    @include('account.partials.top', ['heading' => 'My orders'])

    <section class="learn-card">
        <header class="card-head">
            <h2>Orders</h2>
        </header>
        <div class="table-scroll">
            <table class="learn-table">
                <thead>
                    <tr><th>Number</th><th>Status</th><th>Total</th><th>Courses</th></tr>
                </thead>
                <tbody>
                    @forelse ($orders as $order)
                        <tr>
                            <td><a href="{{ route('account.orders.show', $order) }}">{{ $order->number }}</a></td>
                            <td><span class="pill {{ $order->status === 'approved' ? 'is-ok' : ($order->status === 'rejected' ? 'is-bad' : 'is-wait') }}">{{ ucfirst($order->status) }}</span></td>
                            <td>{{ (float) $order->total > 0 ? $order->money($order->total) : 'Request quotation' }}</td>
                            <td>{{ $order->items->pluck('title')->join(', ') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4">No orders yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <p class="muted">
            @if ($orders->total() === 0)
                Showing 0 results
            @else
                Showing {{ $orders->firstItem() }} to {{ $orders->lastItem() }} of {{ $orders->total() }} results
            @endif
        </p>
        {{ $orders->links() }}
    </section>
@endsection
