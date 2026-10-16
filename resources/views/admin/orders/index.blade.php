@extends('layouts.admin')

@section('content')
    <section class="panel-head">
        <div>
            <h1>Orders</h1>
            <p>Review course orders and payment status.</p>
        </div>
    </section>
    <section class="panel list-panel">
        <form class="list-tools" method="get">
            <label class="search-shell">
                <span class="visually-hidden">Search</span>
                @include('components.admin.icon', ['name' => 'search'])
                <input class="input" type="search" name="q" value="{{ request('q') }}" placeholder="Search order or student">
            </label>
            <select class="select" name="status" aria-label="Status" data-auto-submit>
                <option value="">All statuses</option>
                <option value="pending" @selected(request('status') === 'pending')>Pending</option>
                <option value="approved" @selected(request('status') === 'approved')>Approved</option>
                <option value="rejected" @selected(request('status') === 'rejected')>Rejected</option>
            </select>
        </form>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Number</th><th>Student</th><th>Placed</th><th>Status</th><th>Payment</th><th>Total</th><th class="cell-actions">Actions</th></tr></thead>
                <tbody>
                    @forelse ($orders as $order)
                        @php
                            $tone = match ($order->status) {
                                'approved' => '',
                                'pending' => 'is-wait',
                                default => 'is-bad',
                            };
                        @endphp
                        <tr>
                            <td><a href="{{ route('admin.orders.show', $order) }}">{{ $order->number }}</a></td>
                            <td>{{ $order->user?->name }}</td>
                            <td>{{ $order->created_at?->format('d M Y') }}</td>
                            <td><span class="tag {{ $tone }}">{{ ucfirst($order->status) }}</span></td>
                            <td>{{ ucfirst(str_replace('_', ' ', $order->payment_status)) }}</td>
                            <td>{{ $order->money($order->total) }}</td>
                            <td>
                                <div class="row-actions">
                                    <a class="act act-view" href="{{ route('admin.orders.show', $order) }}">
                                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M2.5 12S6 5.5 12 5.5 21.5 12 21.5 12 18 18.5 12 18.5 2.5 12 2.5 12Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><circle cx="12" cy="12" r="2.8" stroke="currentColor" stroke-width="1.8"/></svg>
                                        View
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7">No orders yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @include('admin.partials.pager', ['paginator' => $orders])
    </section>
@endsection
