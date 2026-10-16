<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Service;
use App\Support\SeoView;
use Illuminate\View\View;

class ServiceController extends Controller
{
    public function index(): View
    {
        $services = Service::query()->active()->with(['plans' => fn ($query) => $query->active()])->orderBy('sort_order')->get();
        $words = [1 => 'One', 2 => 'Two', 3 => 'Three', 4 => 'Four', 5 => 'Five', 6 => 'Six'];

        return view('public.services.index', [
            'services' => $services,
            'countLabel' => $words[$services->count()] ?? (string) $services->count(),
            'title' => 'Services | '.config('app.name'),
            'description' => 'Application writing, self-paced courses, exam practice, PDUs, certification advisory, and corporate training from EdHut Solutions.',
        ]);
    }

    public function show(Service $service): View
    {
        abort_unless($service->isActive(), 404);
        $service->load(['seo', 'plans' => fn ($query) => $query->active()]);
        $seo = SeoView::for($service, $service->title.' | '.config('app.name'), $service->short_description);

        if ($service->slug === 'corporate-training') {
            $order = [
                'pmp-certification-training',
                'pgmp-certification-training',
                'pfmp-certification-training',
                'pmi-rmp-certification-training',
            ];

            return view('public.services.corporate', [
                'service' => $service,
                ...$seo,
                'courses' => Course::query()->active()->get()
                    ->sortBy(fn (Course $course) => array_search($course->slug, $order, true) === false ? 99 : array_search($course->slug, $order, true))
                    ->values(),
            ]);
        }

        return view('public.services.show', [
            'service' => $service,
            'points' => $this->featurePoints($service),
            'hero' => $this->hero($service),
            'others' => Service::query()
                ->active()
                ->whereKeyNot($service->id)
                ->with(['plans' => fn ($query) => $query->active()])
                ->orderBy('sort_order')
                ->limit(4)
                ->get(),
            ...$seo,
        ]);
    }

    /**
     * @return list<string>
     */
    private function featurePoints(Service $service): array
    {
        preg_match_all('/<li>(.*?)<\/li>/is', (string) $service->description, $matches);
        $points = collect($matches[1] ?? [])
            ->map(fn ($item) => trim(html_entity_decode(strip_tags($item))))
            ->filter()
            ->values();

        if ($service->slug === 'application-writing-support') {
            $points->push('100% money back if PMI rejects the application.');
        }

        if ($points->isEmpty() && $service->slug === 'certification-advisory') {
            return [
                'A focused 30-minute one-on-one consultation.',
                'Advisors look at background, experience, and career objectives.',
                'A recommendation for the PMI certification that fits the current role and future goals.',
            ];
        }

        return $points->all();
    }

    /**
     * @return array{headline: string, nodes: list<string>}
     */
    private function hero(Service $service): array
    {
        return match ($service->slug) {
            'application-writing-support' => [
                'headline' => 'Write a PMI application with a review before you submit.',
                'nodes' => ['Expert Review', 'Submission Support', 'Eligibility Guidance', 'Application Writing Support'],
            ],
            'self-learning-course' => [
                'headline' => 'Learn on your schedule with content aligned to the published standard.',
                'nodes' => ['Latest Published Standard', 'Exam-Aligned Content', 'Learn at Your Pace', 'Examples and Case Studies'],
            ],
            'exam-simulation-tool' => [
                'headline' => 'Practice with question banks checked by certified trainers.',
                'nodes' => ['Domain Question Banks', 'Exam-Style Patterns', 'Trainer-Checked Answers', 'A Reason for Every Option'],
            ],
            'pdu-course' => [
                'headline' => 'Keep the credential current with a PMI-approved PDU course.',
                'nodes' => ['60-Hour PDU Course', 'Three Months of Access', 'Email Query Support', 'Help Claiming PDUs'],
            ],
            'certification-advisory' => [
                'headline' => 'Choose the PMI certification that fits the role you want next.',
                'nodes' => ['30-Minute Consultation', 'Eligibility Review', 'A Recommended Path', 'Career Objectives'],
            ],
            default => [
                'headline' => $service->title,
                'nodes' => ['PMI Authorized Trainers', 'Online or In Person', 'Advisor Support', 'Published PMI Standards'],
            ],
        };
    }
}
