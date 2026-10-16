@extends('layouts.admin')

@section('content')
    <section class="panel-head">
        <div>
            <h1>Service prices</h1>
            <p>{{ $service->title }}</p>
        </div>
        <div class="row-actions">
            <a class="btn btn-outline" href="{{ route('admin.services.materials', $service->id) }}">PDFs and videos</a>
            <a class="btn btn-outline" href="{{ route('admin.modules.index', 'services') }}">All services</a>
        </div>
    </section>
    <p class="material-note">Each active price is a certification track in the pricing popup on the homepage and on the service page. The lowest one is shown as "Starting at". With no active price, the service shows "On request".</p>

    <section class="material-block">
        <div class="panel-head">
            <div>
                <h2>Prices</h2>
                <p>Learners pick a track and buy it from the popup.</p>
            </div>
            <a class="btn btn-blue" href="{{ route('admin.services.plans.create', $service->id) }}">+ New price</a>
        </div>
        <div class="panel list-panel">
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Track</th>
                            <th>Price</th>
                            <th>Order</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($service->plans as $plan)
                            <tr>
                                <td>{{ $plan->label }}</td>
                                <td>
                                    {{ $plan->money($plan->price) }}
                                    @if ($plan->hasDiscount())
                                        <span class="meta"><s>{{ $plan->money($plan->compare_price) }}</s></span>
                                    @endif
                                </td>
                                <td>{{ $plan->sort_order }}</td>
                                <td><span class="tag {{ $plan->status === 'active' ? '' : 'is-off' }}">{{ ucfirst($plan->status) }}</span></td>
                                <td>
                                    <div class="row-actions">
                                        <a class="act" href="{{ route('admin.services.plans.edit', [$service->id, $plan->id]) }}">Edit</a>
                                        <form method="post" action="{{ route('admin.services.plans.destroy', [$service->id, $plan->id]) }}" data-confirm="Delete this price?">
                                            @csrf
                                            @method('DELETE')
                                            <button class="act act-danger" type="submit">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5">No price has been added for this service yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>
@endsection
