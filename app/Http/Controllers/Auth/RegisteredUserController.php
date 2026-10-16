<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register', ['title' => 'Create account | '.config('app.name')]);
    }

    public function store(RegisterRequest $request): RedirectResponse
    {
        $user = User::create([
            'name' => $request->string('name')->toString(),
            'email' => $request->string('email')->toString(),
            'phone' => $request->input('phone'),
            'password' => $request->string('password')->toString(),
            'role' => UserRole::Student,
            'status' => 'active',
        ]);

        event(new Registered($user));
        Auth::login($user);

        return redirect()->route('verification.notice')
            ->with('toast', 'Your account has been created. Please verify your email address.')
            ->with('toast_ok', true);
    }
}
