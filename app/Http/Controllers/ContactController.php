<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactRequest;
use App\Models\ContactEnquiry;
use App\Services\Settings\SiteSettings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function create(): View
    {
        return view('public.contact', [
            'title' => 'Contact | '.config('app.name'),
        ]);
    }

    public function store(ContactRequest $request, SiteSettings $settings): RedirectResponse
    {
        $data = $request->validated();
        if (! empty($data['company'])) {
            $data['message'] = "Company: {$data['company']}\n\n".$data['message'];
        }
        unset($data['company']);
        $data['subject'] = ($data['subject'] ?? null) ?: 'Website enquiry';
        $data['status'] = 'new';
        $enquiry = ContactEnquiry::create($data);

        $to = $settings->get('email');
        if ($to !== '') {
            try {
                Mail::raw(
                    "Name: {$enquiry->name}\nEmail: {$enquiry->email}\nPhone: {$enquiry->phone}\n\n{$enquiry->message}",
                    function ($message) use ($to, $enquiry) {
                        $message->to($to)->subject('EdHut enquiry: '.$enquiry->subject);
                    }
                );
            } catch (\Throwable $exception) {
                report($exception);
            }
        }

        return back()->with('toast', 'Thank you. Your enquiry has been received.')->with('toast_ok', true);
    }
}
