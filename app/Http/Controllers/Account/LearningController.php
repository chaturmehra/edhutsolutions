<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LearningController extends Controller
{
    public function index(Request $request): View
    {
        $filter = $request->string('status')->toString();
        if (! in_array($filter, ['all', 'approved', 'pending'], true)) {
            $filter = 'all';
        }

        $user = $request->user();
        $enrollments = $user->enrollments()->with(['course' => fn ($query) => $query->withCount('lessons')])->latest('enrolled_at')->get();
        $pending = $user->orders()
            ->where('status', 'pending')
            ->whereHas('items', fn ($items) => $items->whereNotNull('course_id'))
            ->with(['items' => fn ($items) => $items->whereNotNull('course_id')->with('course')])
            ->latest()
            ->get();

        return view('account.courses.index', [
            'filter' => $filter,
            'enrollments' => match ($filter) {
                'pending' => collect(),
                'approved' => $enrollments->where('status', 'active')->values(),
                default => $enrollments,
            },
            'pending' => $filter === 'approved' ? collect() : $pending,
            'counts' => [
                'all' => $enrollments->count() + $pending->sum(fn ($order) => $order->items->count()),
                'approved' => $enrollments->where('status', 'active')->count(),
                'pending' => $pending->sum(fn ($order) => $order->items->count()),
            ],
        ]);
    }

    public function show(Request $request, Course $course): View
    {
        $this->authorize('viewContent', $course);
        abort_unless($course->isActive(), 404);

        $course->load([
            'sections' => fn ($query) => $query->active()->with(['lessons' => fn ($lessons) => $lessons->active()]),
            'pdfs' => fn ($query) => $query->active()->orderBy('sort_order'),
            'videos' => fn ($query) => $query->active()->orderBy('sort_order'),
            'quizzes' => fn ($query) => $query->active()
                ->withCount(['questions as question_count' => fn ($questions) => $questions->active()])
                ->orderBy('title'),
        ]);

        $tests = $course->quizzes->filter(fn (Quiz $quiz) => $quiz->question_count > 0);
        $open = QuizAttempt::query()
            ->where('user_id', $request->user()->id)
            ->whereIn('quiz_id', $tests->modelKeys())
            ->whereNull('submitted_at')
            ->pluck('quiz_id')
            ->all();

        return view('account.courses.show', [
            'course' => $course,
            'mockTests' => $tests->filter(fn (Quiz $quiz) => $quiz->isMock())->values(),
            'practiceTests' => $tests->reject(fn (Quiz $quiz) => $quiz->isMock())->values(),
            'openQuizIds' => $open,
        ]);
    }
}
