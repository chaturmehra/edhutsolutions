@php
    $fee = fn ($amount) => (float) $amount > 0 ? $pricing->format($amount) : 'Request quotation';
    $total = $cartItems->sum(fn ($item) => $pricing->amount($item->price));
    $totalLabel = $total > 0 ? $pricing->formatConverted($total) : 'Request quotation';
@endphp
<div class="cart-drawer" id="cart-drawer" data-cart-drawer aria-hidden="true">
    <button class="cart-scrim" type="button" data-cart-close aria-label="Close cart"></button>
    <aside class="cart-panel" role="dialog" aria-modal="true" aria-labelledby="cart-title">
        <div class="cart-head">
            <h2 id="cart-title">Your cart <span class="cart-badge">{{ $cartItems->count() }}</span></h2>
            <button class="cart-close" type="button" data-cart-close aria-label="Close">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M6 6l12 12M18 6 6 18" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
            </button>
        </div>
        <div class="cart-body">
            @if ($cartItems->isEmpty())
                <p class="cart-empty">Your cart is empty. Add a course, then submit the seat request for approval.</p>
                <a class="btn btn-primary" href="{{ route('courses.index') }}">Browse courses</a>
            @else
                <ul class="cart-list">
                    @foreach ($cartItems as $item)
                        <li class="drawer-item">
                            <span class="drawer-badge">{{ $item->badge() }}</span>
                            <div>
                                <strong>{{ $item->label() }}</strong>
                                <span>{{ $fee($item->price) }}</span>
                            </div>
                            <form method="post" action="{{ route('cart.destroy', $item) }}">
                                @csrf
                                @method('DELETE')
                                <button class="cart-remove" type="submit" aria-label="Remove {{ $item->label() }}">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 7h16M9 7V5h6v2M8 7l1 13h6l1-13" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </button>
                            </form>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
        @if ($cartItems->isNotEmpty())
            <section class="drawer-summary">
                <h3>Order summary</h3>
                @foreach ($cartItems as $item)
                    <p><span>{{ $item->servicePlan ? $item->servicePlan->service?->title.' · '.$item->servicePlan->label : $item->badge().($item->package ? ' · '.$item->package->name : '') }}</span><strong>{{ $fee($item->price) }}</strong></p>
                @endforeach
                <p class="drawer-total"><span>Total</span><strong>{{ $totalLabel }}</strong></p>
                <a class="btn btn-primary" href="{{ route('checkout.create') }}">Proceed to Checkout</a>
            </section>
        @endif
    </aside>
</div>
