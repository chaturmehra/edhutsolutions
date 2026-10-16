<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Course;
use App\Models\ServicePlan;
use App\Services\Cart\CartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    public function __construct(private CartService $carts) {}

    public function index(): View
    {
        if ($this->carts->pruneOwned() > 0) {
            session()->now('toast', 'Items you already bought were removed from your cart.');
            session()->now('toast_ok', false);
        }

        return view('public.cart', [
            'title' => 'Cart | '.config('app.name'),
            'items' => $this->carts->items(),
        ]);
    }

    public function store(Request $request, Course $course): RedirectResponse
    {
        $package = null;
        if ($request->filled('package')) {
            $package = $course->packages()->active()->whereKey($request->integer('package'))->firstOrFail();
        } elseif ($this->carts->isPurchased($course)) {
            return back()->with('toast', 'You already bought '.$course->shortLabel().'.')->with('toast_ok', false);
        } elseif ($course->packages()->active()->exists()) {
            return redirect()->to(route('courses.show', $course).'#packages')
                ->with('toast', 'Choose a package for '.$course->shortLabel().'.')->with('toast_ok', false);
        }

        if ($package && $this->carts->isPurchased($course, $package)) {
            return back()->with('toast', 'You already bought the '.$course->shortLabel().' '.$package->name.' package.')->with('toast_ok', false);
        }

        $schedule = null;
        if ($request->filled('schedule')) {
            $schedule = $course->schedules()->active()->upcoming()->whereKey($request->integer('schedule'))->firstOrFail();
        }

        $buyNow = $request->boolean('buy_now');
        $line = $this->carts->line($course);
        $unchanged = $line
            && (int) $line->course_package_id === (int) $package?->id
            && (int) $line->course_schedule_id === (int) $schedule?->id;

        if ($line && ($unchanged || ! $package)) {
            if ($buyNow) {
                return redirect()->route('checkout.create');
            }

            return back()->with('toast', $course->shortLabel().' is already in your cart.')->with('toast_ok', false);
        }

        $this->carts->add($course, $package, $schedule);

        $label = $package ? $course->shortLabel().' '.$package->name.' package' : $course->shortLabel();
        if ($schedule) {
            $label .= ' ('.$schedule->rangeLabel().')';
        }
        $message = $line ? 'Your cart now has '.$label.'.' : $label.' was added to your cart.';

        if ($buyNow) {
            return redirect()->route('checkout.create')->with('toast', $message)->with('toast_ok', true);
        }

        return back()->with('toast', $message)->with('toast_ok', true);
    }

    public function storePlan(Request $request, ServicePlan $plan): RedirectResponse
    {
        $plan->load('service');
        $label = $plan->service->title.' ('.$plan->label.')';

        if ($this->carts->isPlanPurchased($plan)) {
            return back()->with('toast', 'You already bought '.$label.'.')->with('toast_ok', false);
        }

        if ($this->carts->hasPlan($plan)) {
            if ($request->boolean('buy_now')) {
                return redirect()->route('checkout.create');
            }

            return back()->with('toast', $label.' is already in your cart.')->with('toast_ok', false);
        }

        $this->carts->addPlan($plan);
        $message = $label.' was added to your cart.';

        if ($request->boolean('buy_now')) {
            return redirect()->route('checkout.create')->with('toast', $message)->with('toast_ok', true);
        }

        return back()->with('toast', $message)->with('toast_ok', true);
    }

    public function destroy(CartItem $item): RedirectResponse
    {
        $this->carts->remove($item);

        return back()->with('toast', 'That item was removed from your cart.')->with('toast_ok', true);
    }
}
