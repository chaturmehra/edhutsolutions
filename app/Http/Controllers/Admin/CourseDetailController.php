<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseResource;
use App\Models\CourseSection;
use App\Models\Lesson;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CourseDetailController extends Controller
{
    public function createModule(int $course): View
    {
        $course = $this->course($course);

        return view('admin.courses.module-form', [
            'course' => $course,
            'module' => null,
            'topics' => '',
            'nextOrder' => (int) $course->sections()->max('sort_order') + 1,
        ]);
    }

    public function storeModule(Request $request, int $course): RedirectResponse
    {
        $course = $this->course($course);
        [$data, $topics] = $this->moduleData($request);
        DB::transaction(function () use ($course, $data, $topics) {
            $this->syncTopics($course->sections()->create($data), $topics);
        });

        return redirect()->route('admin.courses.materials', $course->id)->with('status', 'Agenda module saved.');
    }

    public function editModule(int $course, int $module): View
    {
        $course = $this->course($course);
        $module = $this->module($course, $module);

        return view('admin.courses.module-form', [
            'course' => $course,
            'module' => $module,
            'topics' => $module->lessons()->active()->pluck('title')->implode("\n"),
            'nextOrder' => 1,
        ]);
    }

    public function updateModule(Request $request, int $course, int $module): RedirectResponse
    {
        $course = $this->course($course);
        $module = $this->module($course, $module);
        [$data, $topics] = $this->moduleData($request);
        DB::transaction(function () use ($module, $data, $topics) {
            $module->update($data);
            $this->syncTopics($module, $topics);
        });

        return redirect()->route('admin.courses.materials', $course->id)->with('status', 'Agenda module updated.');
    }

    public function destroyModule(int $course, int $module): RedirectResponse
    {
        $course = $this->course($course);
        $this->module($course, $module)->delete();

        return redirect()->route('admin.courses.materials', $course->id)->with('status', 'Agenda module removed.');
    }

    public function createResource(int $course): View
    {
        $course = $this->course($course);

        return view('admin.courses.resource-form', [
            'course' => $course,
            'resource' => null,
            'nextOrder' => (int) $course->resources()->max('sort_order') + 1,
        ]);
    }

    public function storeResource(Request $request, int $course): RedirectResponse
    {
        $course = $this->course($course);
        $data = $this->resourceData($request);
        if ($request->hasFile('file')) {
            $data += $this->storePdf($request->file('file'));
        }
        $course->resources()->create($data);

        return redirect()->route('admin.courses.materials', $course->id)->with('status', 'Resource saved.');
    }

    public function editResource(int $course, int $resource): View
    {
        $course = $this->course($course);

        return view('admin.courses.resource-form', [
            'course' => $course,
            'resource' => $this->resource($course, $resource),
            'nextOrder' => 1,
        ]);
    }

    public function updateResource(Request $request, int $course, int $resource): RedirectResponse
    {
        $course = $this->course($course);
        $resource = $this->resource($course, $resource);
        $data = $this->resourceData($request);
        if ($request->hasFile('file')) {
            $this->deletePdf($resource->file_path);
            $data += $this->storePdf($request->file('file'));
        } elseif ($request->boolean('remove_file')) {
            $this->deletePdf($resource->file_path);
            $data += ['file_path' => null, 'original_name' => null, 'file_size' => 0];
        }
        $resource->update($data);

        return redirect()->route('admin.courses.materials', $course->id)->with('status', 'Resource updated.');
    }

    public function destroyResource(int $course, int $resource): RedirectResponse
    {
        $course = $this->course($course);
        $resource = $this->resource($course, $resource);
        $this->deletePdf($resource->file_path);
        $resource->delete();

        return redirect()->route('admin.courses.materials', $course->id)->with('status', 'Resource removed.');
    }

    private function course(int $course): Course
    {
        return Course::query()->findOrFail($course);
    }

    private function module(Course $course, int $module): CourseSection
    {
        return $course->sections()->findOrFail($module);
    }

    private function resource(Course $course, int $resource): CourseResource
    {
        return $course->resources()->findOrFail($resource);
    }

    /**
     * @return array{0: array{title: string, sort_order: int, status: string}, 1: array<int, string>}
     */
    private function moduleData(Request $request): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'topics' => ['required', 'string', 'max:10000'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ], [
            'topics.required' => 'Add at least one topic, one per line.',
        ]);

        $topics = collect(preg_split('/\R/', $data['topics']))
            ->map(fn ($topic) => Str::limit(trim($topic), 250, ''))
            ->filter()
            ->values()
            ->all();

        return [[
            'title' => $data['title'],
            'sort_order' => (int) ($data['sort_order'] ?? 0),
            'status' => $data['status'],
        ], $topics];
    }

    /**
     * @param  array<int, string>  $topics
     */
    private function syncTopics(CourseSection $module, array $topics): void
    {
        $existing = $module->lessons()->active()->get()->values();
        foreach ($topics as $index => $title) {
            $lesson = $existing->get($index);
            if ($lesson) {
                $lesson->update(['title' => $title, 'sort_order' => $index + 1]);
                continue;
            }
            $module->lessons()->create([
                'title' => $title,
                'slug' => $this->lessonSlug($module, $title),
                'sort_order' => $index + 1,
                'status' => 'active',
            ]);
        }
        $existing->slice(count($topics))->each(fn (Lesson $lesson) => $lesson->delete());
    }

    private function lessonSlug(CourseSection $module, string $title): string
    {
        $base = Str::slug($title) ?: 'topic';
        $slug = $base;
        $suffix = 2;
        while (Lesson::withTrashed()->where('course_section_id', $module->id)->where('slug', $slug)->exists()) {
            $slug = $base.'-'.$suffix;
            $suffix++;
        }

        return $slug;
    }

    /**
     * @return array{title: string, description: string|null, sort_order: int, status: string}
     */
    private function resourceData(Request $request): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'file' => ['nullable', 'file', 'mimes:pdf', 'max:20480'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);

        return [
            'title' => $data['title'],
            'description' => filled($data['description'] ?? null) ? trim($data['description']) : null,
            'sort_order' => (int) ($data['sort_order'] ?? 0),
            'status' => $data['status'],
        ];
    }

    /**
     * @return array{file_path: string, original_name: string, file_size: int}
     */
    private function storePdf(UploadedFile $file): array
    {
        return [
            'file_path' => $file->storeAs('courses/resources', Str::uuid()->toString().'.pdf', 'local'),
            'original_name' => $file->getClientOriginalName(),
            'file_size' => $file->getSize() ?: 0,
        ];
    }

    private function deletePdf(?string $path): void
    {
        if (! $path || str_contains($path, '..') || ! str_starts_with($path, 'courses/resources/')) {
            return;
        }
        Storage::disk('local')->delete($path);
    }
}
