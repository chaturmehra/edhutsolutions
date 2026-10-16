<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Course;
use App\Models\Service;
use App\Models\User;
use App\Services\Cart\CartService;
use App\Support\SeoView;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CourseController extends Controller
{
    public function index(Request $request): View
    {
        $sort = $request->string('sort')->toString();
        $sort = in_array($sort, ['title', 'latest', 'path'], true) ? $sort : 'path';
        $pathOrder = "CASE slug WHEN 'pmp-certification-training' THEN 1 WHEN 'pgmp-certification-training' THEN 2 WHEN 'pfmp-certification-training' THEN 3 WHEN 'pmi-rmp-certification-training' THEN 4 ELSE 9 END";

        $courses = Course::query()
            ->active()
            ->with('category')
            ->when($request->filled('q'), function ($query) use ($request) {
                $term = '%'.$request->string('q')->toString().'%';
                $query->where(function ($inner) use ($term) {
                    $inner->where('title', 'like', $term)
                        ->orWhere('short_description', 'like', $term);
                });
            })
            ->when($request->filled('category'), function ($query) use ($request) {
                $query->whereHas('category', fn ($category) => $category->where('slug', $request->string('category')->toString())->active());
            })
            ->when($sort === 'latest', fn ($query) => $query->latest())
            ->when($sort === 'title', fn ($query) => $query->orderBy('title'))
            ->when($sort === 'path', fn ($query) => $query->orderByRaw($pathOrder))
            ->paginate(9)
            ->withQueryString();

        return view('public.courses.index', [
            'courses' => $courses,
            'owned' => auth()->user()?->purchasedCourseIds() ?? ['enrolled' => [], 'pending' => []],
            'categories' => Category::query()->active()->orderBy('sort_order')->get(),
        ]);
    }

    public function show(Course $course, CartService $carts): View
    {
        abort_unless($course->isActive(), 404);

        $course->load([
            'seo',
            'category',
            'faqs' => fn ($query) => $query->active(),
            'packages' => fn ($query) => $query->active(),
            'sections' => fn ($query) => $query->active()->with(['lessons' => fn ($lessons) => $lessons->active()]),
            'resources' => fn ($query) => $query->active(),
        ]);

        $enrolled = auth()->user()?->can('viewContent', $course) ?? false;
        [$ownedPackages, $pendingPackages] = $this->packageState($course, $enrolled);

        return view('public.courses.show', [
            'course' => $course,
            'roadmapCourses' => Course::query()->active()->orderByRaw("CASE slug WHEN 'pmp-certification-training' THEN 1 WHEN 'pgmp-certification-training' THEN 2 WHEN 'pfmp-certification-training' THEN 3 WHEN 'pmi-rmp-certification-training' THEN 4 ELSE 9 END")->get(),
            'services' => Service::query()->active()->with(['plans' => fn ($query) => $query->active()])->orderBy('sort_order')->get(),
            'testimonials' => $course->testimonials()->active()->get(),
            'trainers' => $course->trainers()->active()->get(),
            'alumni' => $course->alumni()->active()->get(),
            'inCart' => $carts->contains($course),
            'cartLine' => $carts->line($course),
            'cohorts' => $course->schedules()->active()->upcoming()->with('trainer')->get(),
            'enrolled' => $enrolled,
            'pendingOrder' => $enrolled ? null : auth()->user()?->pendingOrderFor($course),
            'ownedPackages' => $ownedPackages,
            'pendingPackages' => $pendingPackages,
            ...SeoView::for($course, $course->title.' | '.config('app.name'), $course->short_description),
        ]);
    }

    /**
     * Which of the course's packages the visitor already owns, and which are waiting on an order.
     *
     * @return array{0: list<int>, 1: array<int, int>} owned package ids, and pending package id => order id
     */
    private function packageState(Course $course, bool $enrolled): array
    {
        $user = auth()->user();
        if (! $user) {
            return [[], []];
        }

        $keys = $user->purchasedPackageKeys();
        $mine = array_values(array_filter($keys['enrolled'], fn (string $key) => str_starts_with($key, $course->id.':')));
        $owned = [];
        if ($enrolled) {
            $owned = $course->packages
                ->filter(fn ($package) => $mine === [] || User::ownsPackage($mine, $course->id, $package->id))
                ->pluck('id')
                ->all();
        }

        $pending = [];
        foreach ($course->packages as $package) {
            if (in_array($package->id, $owned, true)) {
                continue;
            }
            $orderId = $keys['pending'][$course->id.':'.$package->id] ?? $keys['pending'][$course->id.':*'] ?? null;
            if ($orderId) {
                $pending[$package->id] = $orderId;
            }
        }

        return [$owned, $pending];
    }

    public function resource(Course $course, int $resource): StreamedResponse
    {
        abort_unless($course->isActive(), 404);
        $resource = $course->resources()->active()->findOrFail($resource);
        abort_unless($resource->hasFile() && Storage::disk('local')->exists($resource->file_path), 404);

        $name = Str::slug(pathinfo((string) ($resource->original_name ?: $resource->title), PATHINFO_FILENAME)) ?: 'resource';

        return Storage::disk('local')->response($resource->file_path, $name.'.pdf', [
            'Content-Type' => 'application/pdf',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}
