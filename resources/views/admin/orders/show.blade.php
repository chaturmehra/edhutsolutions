@extends('layouts.admin')

@section('content')
    @php
        $tone = match ($order->status) {
            'approved' => '',
            'pending' => 'is-wait',
            default => 'is-bad',
        };
    @endphp
    <section class="panel-head">
        <div>
            <h1>{{ $order->number }}</h1>
            <p>{{ $order->user?->name }} · {{ $order->user?->email }}</p>
        </div>
        <a class="btn btn-outline" href="{{ route('admin.orders.index') }}">Back</a>
    </section>

    <section class="panel form-panel">
        <h2>Order details</h2>
        <dl class="order-facts">
            <div><dt>Status</dt><dd><span class="tag {{ $tone }}">{{ ucfirst($order->status) }}</span></dd></div>
            <div><dt>Placed</dt><dd>{{ $order->created_at?->format('d M Y, h:i A') }}</dd></div>
            <div><dt>Payment</dt><dd>{{ ucfirst(str_replace('_', ' ', $order->payment_status)) }}</dd></div>
            <div><dt>Total</dt><dd>{{ $order->money($order->total) }}</dd></div>
            <div><dt>Student</dt><dd>{{ $order->user?->name ?? '—' }}</dd></div>
            <div><dt>Phone</dt><dd>{{ $order->phone ?: ($order->user?->phone ?: '—') }}</dd></div>
            <div><dt>Country</dt><dd>{{ $order->country?->name ?? ($order->country_code ?: '—') }}</dd></div>
            @if ($order->state)
                <div><dt>State</dt><dd>{{ $order->state }}</dd></div>
            @endif
            @if ($order->approved_at || $order->rejected_at)
                <div>
                    <dt>{{ $order->status === 'approved' ? 'Approved' : 'Rejected' }}</dt>
                    <dd>{{ ($order->approved_at ?? $order->rejected_at)?->format('d M Y') }}{{ $order->approver ? ' by '.$order->approver->name : '' }}</dd>
                </div>
            @endif
        </dl>
    </section>

    <section class="panel form-panel">
        <h2>Courses</h2>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Course</th><th>Price</th></tr></thead>
                <tbody>
                    @foreach ($order->items as $item)
                        <tr>
                            <td>{{ $item->title }}</td>
                            <td>{{ (float) $item->price > 0 ? $order->money($item->price) : 'Request quotation' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if ($order->admin_note)
            <p class="meta">Note: {{ $order->admin_note }}</p>
        @endif
    </section>

    @if ($order->status === 'pending')
        <section class="order-actions">
            <form class="panel form-panel" method="post" action="{{ route('admin.orders.approve', $order) }}">
                @csrf
                <h2>Approve</h2>
                <label class="field"><span>Note</span><textarea class="textarea" name="admin_note"></textarea></label>
                <div class="form-actions">
                    <button class="btn btn-purple" type="submit">Approve and enroll</button>
                </div>
            </form>
            <form class="panel form-panel" method="post" action="{{ route('admin.orders.reject', $order) }}" data-confirm="Reject this order?">
                @csrf
                <h2>Reject</h2>
                <label class="field"><span>Reason</span><textarea class="textarea" name="admin_note"></textarea></label>
                <div class="form-actions">
                    <button class="btn btn-outline" type="submit">Reject</button>
                </div>
            </form>
        </section>
    @endif
@endsection
