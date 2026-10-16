<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CoursePdf;
use App\Models\CourseVideo;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $user = auth()->user();
        $enrollments = $user->enrollments()->with(['course' => fn ($query) => $query->withCount('lessons')])->latest('enrolled_at')->get();
        $active = $enrollments->where('status', 'active')->values();
        $courseIds = $active->pluck('course_id')->filter()->values();
        $orders = $user->orders()->with('items')->latest()->limit(6)->get();
        $attempts = $user->quizAttempts()->with('quiz')->whereNotNull('submitted_at')->latest('submitted_at')->limit(6)->get();

        $serviceIds = $user->serviceEnrollments()
            ->where('status', 'active')
            ->whereHas('service', fn ($query) => $query->active())
            ->pluck('service_id');
        $owned = fn ($query) => $query->whereIn('course_id', $courseIds)->orWhereIn('service_id', $serviceIds);

        $files = collect();
        if ($courseIds->isNotEmpty() || $serviceIds->isNotEmpty()) {
            $files = CoursePdf::query()->active()->where($owned)->with(['course', 'service'])->orderBy('title')->limit(3)->get()
                ->map(fn (CoursePdf $pdf) => [
                    'kind' => 'PDF',
                    'title' => $pdf->title,
                    'course' => $pdf->course?->shortLabel() ?? $pdf->service?->title,
                    'href' => route('learn.pdf', $pdf),
                ])
                ->concat(
                    CourseVideo::query()->active()->where($owned)->with(['course', 'service'])->orderBy('title')->limit(3)->get()
                        ->map(fn (CourseVideo $video) => [
                            'kind' => 'Video',
                            'title' => $video->title,
                            'course' => $video->course?->shortLabel() ?? $video->service?->title,
                            'href' => match (true) {
                                $video->service !== null => route('learn.services.show', $video->service),
                                $video->course !== null => route('learn.show', $video->course),
                                default => route('account.courses.index'),
                            },
                        ])
                );
        }

        $activity = collect();
        foreach ($orders as $order) {
            $activity->push([
                'title' => $order->items->pluck('title')->join(', ') ?: $order->number,
                'meta' => ucfirst($order->status).' · '.$order->number,
                'tone' => $order->status === 'approved' ? 'ok' : ($order->status === 'rejected' ? 'bad' : 'wait'),
                'at' => $order->created_at,
                'href' => route('account.orders.show', $order),
            ]);
        }
        foreach ($attempts as $attempt) {
            if (! $attempt->quiz) {
                continue;
            }
            $activity->push([
                'title' => $attempt->quiz->title,
                'meta' => ($attempt->passed ? 'Passed' : 'Not passed').' · '.$this->percent($attempt->percentage),
                'tone' => $attempt->passed ? 'ok' : 'bad',
                'at' => $attempt->submitted_at,
                'href' => route('quizzes.results', [$attempt->quiz, $attempt]),
            ]);
        }

        $enrolledIds = $enrollments->pluck('course_id')
            ->merge($orders->flatMap(fn ($order) => $order->items->pluck('course_id')))
            ->filter();

        return view('account.dashboard', [
            'enrolledCount' => $enrollments->count(),
            'openCount' => $active->count(),
            'pendingCount' => $user->orders()->where('status', 'pending')->count(),
            'passedCount' => $user->quizAttempts()->where('passed', true)->count(),
            'files' => $files,
            'current' => $active->first(),
            'activity' => $activity->sortByDesc('at')->take(5)->values(),
            'recent' => $enrollments->take(3),
            'recommended' => Course::query()->active()
                ->when($enrolledIds->isNotEmpty(), fn ($query) => $query->whereNotIn('id', $enrolledIds))
                ->orderBy('title')
                ->limit(3)
                ->get(),
            'latestAttempt' => $attempts->first(),
            'latestOrder' => $orders->first(),
        ]);
    }

    private function percent(mixed $value): string
    {
        return rtrim(rtrim(number_format((float) $value, 1), '0'), '.').'%';
    }
}
