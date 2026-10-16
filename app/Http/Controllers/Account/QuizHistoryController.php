<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use Illuminate\Http\Request;
use Illuminate\View\View;

class QuizHistoryController extends Controller
{
    public function index(Request $request): View
    {
        $filter = $request->string('status')->toString();
        if (! in_array($filter, ['all', 'available', 'progress', 'done'], true)) {
            $filter = 'all';
        }

        $user = $request->user();
        $courseIds = $user->enrollments()->where('status', 'active')->pluck('course_id');
        $quizzes = Quiz::query()
            ->active()
            ->whereIn('course_id', $courseIds)
            ->with('course')
            ->withCount(['questions' => fn ($query) => $query->active()])
            ->orderBy('title')
            ->get();
        $attempts = $user->quizAttempts()->get()->groupBy('quiz_id');

        $cards = $quizzes->map(function (Quiz $quiz) use ($attempts) {
            $mine = $attempts->get($quiz->id, collect());
            $open = $mine->first(fn ($attempt) => $attempt->submitted_at === null);
            $latest = $mine->filter(fn ($attempt) => $attempt->submitted_at !== null)->sortByDesc('submitted_at')->first();
            $state = $open ? 'progress' : ($latest ? 'done' : 'available');

            return [
                'quiz' => $quiz,
                'state' => $state,
                'open' => $open,
                'latest' => $latest,
            ];
        });

        return view('account.quizzes.index', [
            'filter' => $filter,
            'counts' => [
                'all' => $cards->count(),
                'available' => $cards->where('state', 'available')->count(),
                'progress' => $cards->where('state', 'progress')->count(),
                'done' => $cards->where('state', 'done')->count(),
            ],
            'cards' => $filter === 'all' ? $cards : $cards->where('state', $filter)->values(),
        ]);
    }
}
