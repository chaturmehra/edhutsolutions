<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use Illuminate\View\View;

class QuizController extends Controller
{
    public function index(): View
    {
        return view('public.quizzes.index', [
            'quizzes' => Quiz::query()
                ->active()
                ->with('course')
                ->withCount(['questions as question_count' => fn ($query) => $query->active()])
                ->orderBy('title')
                ->paginate(12),
        ]);
    }
}
