<?php

namespace App\Providers;

use App\Contracts\PaymentService;
use App\Services\Cart\CartService;
use App\Services\Payments\ManualPaymentService;
use App\Services\Pricing\Pricing;
use App\Services\Pricing\VisitorCountry;
use App\View\Composers\AccountComposer;
use App\View\Composers\SiteComposer;
use Illuminate\Auth\Events\Login;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(PaymentService::class, ManualPaymentService::class);
        $this->app->singleton(VisitorCountry::class);
        $this->app->singleton(Pricing::class);
    }

    public function boot(): void
    {
        Password::defaults(fn () => Password::min(8)->mixedCase()->numbers());
        Paginator::defaultView('pagination.edhut');

        Event::listen(Login::class, function (Login $event) {
            app(CartService::class)->mergeSession(session()->getId(), $event->user);
        });

        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(5)->by(strtolower((string) $request->input('email')).'|'.$request->ip());
        });

        View::composer('layouts.user', AccountComposer::class);

        View::composer([
            'layouts.*',
            'public.*',
            'auth.*',
            'account.*',
            'admin.*',
            'components.*',
        ], SiteComposer::class);
    }
}
