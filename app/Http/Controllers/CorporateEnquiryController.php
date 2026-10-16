<?php

namespace App\Http\Controllers;

use App\Mail\CorporateEnquiryMail;
use App\Models\ContactEnquiry;
use App\Models\Course;
use App\Services\Settings\SiteSettings;
use App\Support\Content;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;

class CorporateEnquiryController extends Controller
{
    public function store(Request $request, SiteSettings $settings): RedirectResponse
    {
        $plan = Content::get('corporate.plan');
        $interests = Course::query()->active()->get()->toBase()->map->shortLabel()->push($plan['interest_other'])->unique()->values()->all();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'digits_between:7,15'],
            'company' => ['required', 'string', 'max:160'],
            'designation' => ['nullable', 'string', 'max:120'],
            'team_size' => ['nullable', Rule::in($plan['team_sizes'])],
            'interest' => ['nullable', Rule::in($interests)],
            'delivery' => ['nullable', Rule::in($plan['deliveries'])],
            'message' => ['nullable', 'string', 'max:5000'],
        ], [], [
            'name' => 'contact name',
            'email' => 'work email',
            'team_size' => 'team size',
            'interest' => 'area of interest',
            'delivery' => 'preferred delivery',
        ]);

        $details = [
            'Contact name' => $data['name'],
            'Work email' => $data['email'],
            'Phone' => $data['phone'] ?? null,
            'Company' => $data['company'],
            'Designation' => $data['designation'] ?? null,
            'Team size' => $data['team_size'] ?? null,
            'Area of interest' => $data['interest'] ?? null,
            'Preferred delivery' => $data['delivery'] ?? null,
        ];
        $summary = collect($details)->except(['Contact name', 'Work email', 'Phone'])
            ->map(fn ($value, $label) => $label.': '.(filled($value) ? $value : '-'))
            ->implode("\n");

        $enquiry = ContactEnquiry::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'subject' => 'Corporate cohort request',
            'message' => $summary."\n\n".(filled($data['message'] ?? null) ? $data['message'] : 'No context provided.'),
            'status' => 'new',
        ]);
        $to = $settings->get('email');
        if ($to !== '') {
            try {
                Mail::to($to)->send(new CorporateEnquiryMail($enquiry, $details, $data['message'] ?? null));
            } catch (\Throwable $exception) {
                report($exception);
            }
        }

        return redirect()->to(route('services.show', 'corporate-training').'#plan-cohort')
            ->with('toast', $plan['success'])
            ->with('toast_ok', true);
    }
}
