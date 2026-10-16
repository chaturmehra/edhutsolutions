<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\Course;
use App\Models\CourseSchedule;
use App\Models\Service;
use App\Models\Testimonial;
use App\Models\VideoStory;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $order = [
            'pmp-certification-training',
            'pgmp-certification-training',
            'pfmp-certification-training',
            'pmi-rmp-certification-training',
        ];

        return view('public.home', [
            'courses' => Course::query()->active()->where('is_featured', true)->get()
                ->sortBy(fn (Course $course) => array_search($course->slug, $order, true) === false ? 99 : array_search($course->slug, $order, true))
                ->values(),
            'services' => Service::query()->active()->with(['plans' => fn ($query) => $query->active()])->orderBy('sort_order')->get(),
            'testimonials' => Testimonial::query()->active()->orderBy('sort_order')->get(),
            'cohorts' => CourseSchedule::query()->active()
                ->upcoming()
                ->whereHas('course', fn ($query) => $query->active())
                ->with(['trainer', 'course.packages' => fn ($query) => $query->active()])
                ->orderBy('starts_on')
                ->limit(6)
                ->get(),
            'videoStories' => VideoStory::query()->active()->orderBy('sort_order')->orderBy('id')->get(),
            'blogs' => Blog::query()->active()->orderByDesc('published_on')->orderBy('id')->limit(3)->get(),
        ]);
    }
}
