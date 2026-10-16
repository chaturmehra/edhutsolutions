<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Users\UserCsvImporter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class UserController extends Controller
{
    public function index(): View
    {
        $search = request()->string('q')->toString();
        $status = request()->string('status')->toString();

        return view('admin.users.index', [
            'users' => User::query()
                ->when($search !== '', fn ($query) => $query->where(function ($inner) use ($search) {
                    $inner->where('name', 'like', '%'.$search.'%')->orWhere('email', 'like', '%'.$search.'%');
                }))
                ->when(in_array($status, ['active', 'blocked'], true), fn ($query) => $query->where('status', $status))
                ->latest()
                ->paginate(10)
                ->withQueryString(),
        ]);
    }

    public function template(): StreamedResponse
    {
        return response()->streamDownload(function () {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, UserCsvImporter::HEADERS);
            fputcsv($out, ['Asha Learner', 'asha@example.com', '9876543210', 'female', 'Bengaluru', 'IN']);
            fclose($out);
        }, 'users-import-template.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function import(Request $request, UserCsvImporter $importer): RedirectResponse
    {
        $request->validate([
            'csv' => ['required', 'file', 'mimes:csv,txt', 'max:5120'],
        ], [
            'csv.required' => 'Choose a CSV file to upload.',
            'csv.mimes' => 'Upload a CSV file.',
        ]);

        $result = $importer->import($request->file('csv'));
        $message = $result['created'].' user'.($result['created'] === 1 ? '' : 's').' imported'
            .($result['skipped'] > 0 ? ', '.$result['skipped'].' skipped' : '')
            .'. Temporary password: '.UserCsvImporter::TEMP_PASSWORD;

        if ($result['errors'] !== []) {
            $message .= ' First issues: '.implode(' ', array_slice($result['errors'], 0, 3));
        }

        return back()->with('status', $message);
    }

    public function status(User $user): RedirectResponse
    {
        abort_if($user->is(auth()->user()), 403);

        $user->update([
            'status' => $user->status === 'active' ? 'blocked' : 'active',
        ]);

        return back()->with('status', $user->status === 'blocked' ? 'User blocked.' : 'User unblocked.');
    }
}
