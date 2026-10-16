<?php

namespace App\Http\Controllers;

use App\Mail\EnrollmentRequestMail;
use App\Models\ContactEnquiry;
use App\Models\Course;
use App\Services\Settings\SiteSettings;
use App\Support\Content;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;

class CourseEnquiryController extends Controller
{
    public function store(Request $request, Course $course, SiteSettings $settings): RedirectResponse
    {
        abort_unless($course->isActive(), 404);

        $copy = Content::get('courses.enroll');
        $certifications = Course::query()->active()->get()->map->shortLabel()->unique()->values()->all();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'digits_between:7,15'],
            'certification' => ['required', Rule::in($certifications)],
            'experience' => ['nullable', Rule::in($copy['experience'])],
            'role' => ['nullable', 'string', 'max:120'],
            'message' => ['nullable', 'string', 'max:5000'],
        ], [], [
            'name' => 'full name',
            'email' => 'work email',
            'role' => 'current role',
        ]);

        $details = [
            'Full name' => $data['name'],
            'Work email' => $data['email'],
            'Phone' => $data['phone'],
            'Certification' => $data['certification'],
            'Experience' => $data['experience'] ?? null,
            'Current role' => $data['role'] ?? null,
            'Course page' => route('courses.show', $course),
        ];
        $summary = collect($details)->except(['Full name', 'Work email', 'Phone'])
            ->map(fn ($value, $label) => $label.': '.(filled($value) ? $value : '-'))
            ->implode("\n");

        $enquiry = ContactEnquiry::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'subject' => 'Enrollment request: '.$data['certification'],
            'message' => $summary."\n\n".(filled($data['message'] ?? null) ? $data['message'] : 'No message provided.'),
            'status' => 'new',
        ]);
        $to = $settings->get('email');
        if ($to !== '') {
            try {
                Mail::to($to)->send(new EnrollmentRequestMail($enquiry, $details, $data['message'] ?? null));
            } catch (\Throwable $exception) {
                report($exception);
            }
        }

        return redirect()->to(route('courses.show', $course).'#enroll')
            ->with('toast', $copy['success'])
            ->with('toast_ok', true);
    }
}
