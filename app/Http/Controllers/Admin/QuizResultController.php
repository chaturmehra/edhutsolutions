<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use Illuminate\Http\Request;
use Illuminate\View\View;

class QuizResultController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->string('q')->toString();
        $quizId = $request->integer('quiz');
        $result = $request->string('result')->toString();

        return view('admin.quiz-results.index', [
            'title' => 'Quiz results',
            'quizzes' => Quiz::query()->orderBy('title')->pluck('title', 'id'),
            'attempts' => QuizAttempt::query()
                ->with(['user', 'quiz'])
                ->when($search !== '', function ($query) use ($search) {
                    $query->whereHas('user', function ($user) use ($search) {
                        $user->where('name', 'like', '%'.$search.'%')
                            ->orWhere('email', 'like', '%'.$search.'%');
                    });
                })
                ->when($quizId > 0, fn ($query) => $query->where('quiz_id', $quizId))
                ->when($result === 'passed', fn ($query) => $query->whereNotNull('submitted_at')->where('passed', true))
                ->when($result === 'failed', fn ($query) => $query->whereNotNull('submitted_at')->where('passed', false))
                ->when($result === 'open', fn ($query) => $query->whereNull('submitted_at'))
                ->latest('id')
                ->paginate(15)
                ->withQueryString(),
        ]);
    }

    public function show(QuizAttempt $attempt): View
    {
        $attempt->load(['user', 'quiz.course', 'answers.question.options']);

        return view('admin.quiz-results.show', [
            'title' => 'Quiz result',
            'attempt' => $attempt,
        ]);
    }
}
