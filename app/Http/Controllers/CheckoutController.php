<?php

namespace App\Http\Controllers;

use App\Contracts\PaymentService;
use App\Models\Country;
use App\Models\Order;
use App\Services\Cart\CartService;
use App\Services\Pricing\Pricing;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function __construct(private CartService $carts, private Pricing $pricing) {}

    public function create(): View|RedirectResponse
    {
        $this->carts->pruneOwned();
        $items = $this->carts->items();

        if ($items->isEmpty()) {
            return redirect()->route('cart.index')->with('toast', 'Your cart is empty.')->with('toast_ok', false);
        }

        $countries = Country::query()->active()->orderBy('name')->get(['code', 'name']);
        $visitor = $this->pricing->country();

        return view('public.checkout', [
            'title' => 'Checkout | '.config('app.name'),
            'items' => $items,
            'total' => $items->sum(fn ($item) => $this->pricing->amount($item->price)),
            'countries' => $countries,
            'selectedCountry' => old('country', $countries->contains('code', $visitor) ? $visitor : null),
        ]);
    }

    public function store(Request $request, PaymentService $payments): RedirectResponse
    {
        $this->carts->pruneOwned();
        $cart = $this->carts->find();
        $items = $this->carts->items();

        if (! $cart || $items->isEmpty()) {
            return redirect()->route('cart.index')->with('toast', 'Your cart is empty.')->with('toast_ok', false);
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
            'country' => ['required', 'string', 'size:2', Rule::exists('countries', 'code')->where('status', 'active')],
        ], [
            'country.required' => 'Select your country.',
            'country.exists' => 'Select your country from the list.',
        ]);

        $request->user()->update([
            'name' => $data['name'],
            'phone' => $data['phone'],
        ]);

        $order = DB::transaction(function () use ($items, $payments, $cart, $data) {
            $prices = $items->mapWithKeys(fn ($item) => [$item->id => $this->pricing->amount($item->price)]);
            $total = round($prices->sum(), 2);
            $order = Order::query()->create([
                'user_id' => auth()->id(),
                'number' => 'EH-'.now()->format('ymd').'-'.strtoupper(Str::random(4)),
                'status' => 'pending',
                'payment_status' => 'not_required',
                'payment_method' => 'manual',
                'subtotal' => $total,
                'total' => $total,
                'currency' => $this->pricing->currency(),
                'phone' => $data['phone'],
                'country_code' => $data['country'],
            ]);

            foreach ($items as $item) {
                $order->items()->create([
                    'course_id' => $item->course_id,
                    'service_plan_id' => $item->service_plan_id,
                    'course_package_id' => $item->course_package_id,
                    'course_schedule_id' => $item->course_schedule_id,
                    'title' => $item->label(),
                    'price' => $prices[$item->id],
                ]);
            }

            $result = $payments->charge($order);
            $order->update([
                'payment_status' => $result->status,
                'payment_method' => 'manual',
            ]);
            $cart->items()->delete();

            return $order;
        });

        $message = $items->contains(fn ($item) => $item->course_id)
            ? 'Your order is placed. You can open the course and continue only after an administrator approves it.'
            : 'Your order is placed. The EdHut team will confirm it after an administrator approves it.';

        return redirect()
            ->route('account.orders.show', $order)
            ->with('toast', $message)
            ->with('toast_ok', true);
    }
}
