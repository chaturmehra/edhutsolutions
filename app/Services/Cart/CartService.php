<?php

namespace App\Services\Cart;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Course;
use App\Models\CoursePackage;
use App\Models\CourseSchedule;
use App\Models\ServicePlan;
use App\Models\User;
use Closure;
use Illuminate\Support\Collection;

class CartService
{
    public function find(): ?Cart
    {
        if ($user = auth()->user()) {
            return Cart::query()->where('user_id', $user->id)->first();
        }

        if ($cartId = session('cart_id')) {
            $cart = Cart::query()->whereKey($cartId)->whereNull('user_id')->first();
            if ($cart) {
                return $cart;
            }
        }

        return Cart::query()
            ->where('session_id', session()->getId())
            ->whereNull('user_id')
            ->first();
    }

    /**
     * @return Collection<int, CartItem>
     */
    public function items(): Collection
    {
        $items = $this->find()?->items()->with(['course', 'package', 'schedule', 'servicePlan.service'])->get() ?? collect();

        if ($items->isNotEmpty() && ($user = auth()->user())) {
            $items = $items->reject($this->ownedBy($user))->values();
        }

        return $items;
    }

    public function contains(Course $course): bool
    {
        return $this->items()->contains('course_id', $course->id);
    }

    /**
     * Whether the signed-in learner already owns, or is waiting on, this course package.
     * Without a package only a whole-course purchase counts.
     */
    public function isPurchased(Course $course, ?CoursePackage $package = null): bool
    {
        $owned = auth()->user()?->purchasedPackageKeys();

        return $owned !== null && User::ownsPackage([...$owned['enrolled'], ...array_keys($owned['pending'])], $course->id, $package?->id);
    }

    public function isPlanPurchased(ServicePlan $plan): bool
    {
        $owned = auth()->user()?->purchasedPlanIds();

        return $owned !== null && in_array($plan->id, [...$owned['enrolled'], ...array_keys($owned['pending'])], true);
    }

    public function pruneOwned(): int
    {
        $user = auth()->user();
        $cart = $user ? $this->find() : null;
        if (! $cart) {
            return 0;
        }

        $owned = $cart->items()->get()->filter($this->ownedBy($user));
        $owned->each->delete();

        return $owned->count();
    }

    public function add(Course $course, ?CoursePackage $package = null, ?CourseSchedule $schedule = null): void
    {
        abort_unless($course->isActive(), 404);
        abort_if($package && ($package->course_id !== $course->id || ! $package->isActive()), 404);
        abort_if($schedule && ($schedule->course_id !== $course->id || ! $schedule->isActive()), 404);

        $cart = $this->resolve();
        session(['cart_id' => $cart->id]);
        $cart->items()->updateOrCreate(
            ['course_id' => $course->id],
            [
                'course_package_id' => $package?->id,
                'course_schedule_id' => $schedule?->id,
                'price' => $package ? $package->price : ($course->price_on_request ? 0 : $course->price),
            ],
        );
    }

    public function line(Course $course): ?CartItem
    {
        return $this->items()->firstWhere('course_id', $course->id);
    }

    public function addPlan(ServicePlan $plan): void
    {
        abort_unless($plan->isActive() && $plan->service?->isActive(), 404);

        $cart = $this->resolve();
        session(['cart_id' => $cart->id]);
        $cart->items()->updateOrCreate(
            ['service_plan_id' => $plan->id],
            ['course_id' => null, 'price' => $plan->price],
        );
    }

    public function hasPlan(ServicePlan $plan): bool
    {
        return $this->items()->contains('service_plan_id', $plan->id);
    }

    public function remove(CartItem $item): void
    {
        $cart = $this->find();
        abort_unless($cart && $item->cart_id === $cart->id, 403);
        $item->delete();
    }

    public function mergeSession(string $sessionId, User $user): void
    {
        $guest = null;

        if ($cartId = session('cart_id')) {
            $guest = Cart::query()->whereKey($cartId)->whereNull('user_id')->first();
        }

        $guest ??= Cart::query()->where('session_id', $sessionId)->whereNull('user_id')->first();

        if (! $guest) {
            return;
        }

        $cart = Cart::query()->firstOrCreate(['user_id' => $user->id]);
        $owned = $this->ownedBy($user);

        foreach ($guest->items as $item) {
            if ($owned($item)) {
                continue;
            }

            if ($item->service_plan_id) {
                $cart->items()->firstOrCreate(['service_plan_id' => $item->service_plan_id], ['price' => $item->price]);

                continue;
            }

            $cart->items()->firstOrCreate(
                ['course_id' => $item->course_id],
                ['course_package_id' => $item->course_package_id, 'course_schedule_id' => $item->course_schedule_id, 'price' => $item->price],
            );
        }

        $guest->delete();
        session()->forget('cart_id');
    }

    /**
     * @return Closure(CartItem): bool true when the learner already owns or awaits that line
     */
    private function ownedBy(User $user): Closure
    {
        $packages = $user->purchasedPackageKeys();
        $packageKeys = [...$packages['enrolled'], ...array_keys($packages['pending'])];
        $plans = $user->purchasedPlanIds();
        $planIds = [...$plans['enrolled'], ...array_keys($plans['pending'])];

        return fn (CartItem $item) => $item->service_plan_id
            ? in_array((int) $item->service_plan_id, $planIds, true)
            : $item->course_id && User::ownsPackage($packageKeys, (int) $item->course_id, $item->course_package_id);
    }

    private function resolve(): Cart
    {
        if ($user = auth()->user()) {
            return Cart::query()->firstOrCreate(['user_id' => $user->id]);
        }

        return Cart::query()->firstOrCreate([
            'session_id' => session()->getId(),
            'user_id' => null,
        ]);
    }
}
