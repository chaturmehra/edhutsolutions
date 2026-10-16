<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AdminLoginController extends Controller
{
    public function create(): View
    {
        return view('admin.login', ['title' => 'Staff sign in']);
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $user = $request->user();

        if (! in_array($user->role, [UserRole::SuperAdmin, UserRole::SeoManager], true)) {
            Auth::logout();
            $request->session()->regenerateToken();

            throw ValidationException::withMessages([
                'email' => 'This sign-in is for EdHut staff only.',
            ]);
        }

        $request->session()->regenerate();

        return redirect()->intended($user->role->homePath());
    }
}
