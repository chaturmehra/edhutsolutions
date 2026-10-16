<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Symfony\Component\HttpFoundation\RedirectResponse as SymfonyRedirect;

class GoogleAuthController extends Controller
{
    public function redirect(): RedirectResponse|SymfonyRedirect
    {
        if (config('edhut.google_login_mode') !== 'live') {
            return $this->loginDemoUser();
        }

        return Socialite::driver('google')->redirect();
    }

    public function callback(): RedirectResponse
    {
        if (config('edhut.google_login_mode') !== 'live') {
            return $this->loginDemoUser();
        }

        $googleUser = Socialite::driver('google')->user();

        $user = User::query()->where('google_id', $googleUser->getId())->first()
            ?? User::query()->where('email', $googleUser->getEmail())->first();

        if (! $user) {
            $user = User::create([
                'name' => $googleUser->getName() ?: 'EdHut Learner',
                'email' => $googleUser->getEmail(),
                'google_id' => $googleUser->getId(),
                'password' => str()->password(32),
                'role' => \App\Enums\UserRole::Student,
                'status' => 'active',
                'email_verified_at' => now(),
            ]);
        } else {
            $user->forceFill([
                'google_id' => $googleUser->getId(),
                'email_verified_at' => $user->email_verified_at ?? now(),
            ])->save();
        }

        if (! $user->isActiveAccount()) {
            return redirect()->route('login')->withErrors([
                'email' => 'This account is blocked. Contact EdHut if you need access restored.',
            ]);
        }

        Auth::login($user, true);
        request()->session()->regenerate();

        return redirect()->intended($user->role->homePath());
    }

    private function loginDemoUser(): RedirectResponse
    {
        $user = User::query()->where('email', config('edhut.demo_google_email'))->first();

        if (! $user || ! $user->isActiveAccount()) {
            return redirect()->route('login')->withErrors([
                'email' => 'The demo Google account is not available.',
            ]);
        }

        Auth::login($user, true);
        request()->session()->regenerate();

        return redirect()->intended($user->role->homePath());
    }
}
