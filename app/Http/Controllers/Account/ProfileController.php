<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(): View
    {
        return view('account.profile', [
            'user' => auth()->user(),
            'countries' => Country::query()->active()->orderBy('name')->get(['code', 'name']),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'phone' => ['nullable', 'string', 'max:30'],
            'gender' => ['nullable', 'string', Rule::in(array_keys(User::GENDERS))],
            'city' => ['nullable', 'string', 'max:120'],
            'country_code' => ['nullable', 'string', 'size:2', Rule::exists('countries', 'code')->where('status', 'active')],
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'avatar' => ['nullable', 'image', 'max:5120'],
        ], [
            'country_code.exists' => 'Select your country from the list.',
        ]);

        $user->fill([
            'name' => $data['name'],
            'phone' => $data['phone'] ?? null,
            'gender' => $data['gender'] ?? null,
            'city' => $data['city'] ?? null,
            'country_code' => $data['country_code'] ?? null,
        ]);

        if ($request->file('avatar') instanceof UploadedFile) {
            $this->storeAvatar($user, $request->file('avatar'));
        }

        if (! empty($data['password'])) {
            $user->password = $data['password'];
        }

        $user->save();

        return redirect()->route('account.profile.edit')->with('status', 'Your profile was saved.');
    }

    private function storeAvatar(User $user, UploadedFile $file): void
    {
        $directory = public_path('assets/images/avatars');
        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $name = Str::uuid()->toString().'.'.strtolower($file->getClientOriginalExtension() ?: 'jpg');
        $file->move($directory, $name);
        $this->deleteLocalAvatar($user->avatar);
        $user->avatar = 'assets/images/avatars/'.$name;
    }

    private function deleteLocalAvatar(?string $path): void
    {
        if ($path && preg_match('#^assets/images/avatars/[A-Za-z0-9\-]+\.[A-Za-z0-9]+$#', $path) && is_file(public_path($path))) {
            unlink(public_path($path));
        }
    }
}
