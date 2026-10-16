<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Redirect;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class RedirectController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'from_path' => ['required', 'string', 'max:255', 'starts_with:/', 'unique:redirects,from_path'],
            'to_path' => ['required', 'string', 'max:255'],
            'status_code' => ['required', 'integer', 'in:301,302'],
        ]);

        abort_if(str_starts_with($data['from_path'], '/admin'), 422);
        $data['is_active'] = true;
        Redirect::query()->create($data);

        return back()->with('status', 'Redirect saved.');
    }

    public function destroy(Redirect $redirect): RedirectResponse
    {
        $redirect->delete();

        return back()->with('status', 'Redirect removed.');
    }
}
