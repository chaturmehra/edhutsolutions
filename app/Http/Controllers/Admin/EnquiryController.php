<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactEnquiry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EnquiryController extends Controller
{
    public function index(): View
    {
        $search = request()->string('q')->toString();
        $status = request()->string('status')->toString();

        return view('admin.enquiries.index', [
            'enquiries' => ContactEnquiry::query()
                ->when($search !== '', function ($query) use ($search) {
                    $query->where(function ($inner) use ($search) {
                        $inner->where('name', 'like', '%'.$search.'%')
                            ->orWhere('email', 'like', '%'.$search.'%')
                            ->orWhere('subject', 'like', '%'.$search.'%');
                    });
                })
                ->when(in_array($status, ['new', 'read'], true), fn ($query) => $query->where('status', $status))
                ->latest()
                ->paginate(10)
                ->withQueryString(),
        ]);
    }

    public function update(ContactEnquiry $enquiry): RedirectResponse
    {
        $enquiry->update(['status' => $enquiry->status === 'new' ? 'read' : 'new']);

        return back()->with('status', 'Enquiry updated.');
    }

    public function markRead(Request $request, ContactEnquiry $enquiry): JsonResponse|RedirectResponse
    {
        if ($enquiry->status !== 'read') {
            $enquiry->update(['status' => 'read']);
        }

        return $request->expectsJson()
            ? response()->json(['status' => 'read'])
            : back()->with('status', 'Enquiry marked as read.');
    }
}
