<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Services\Quizzes\QuizScorer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class QuizTakeController extends Controller
{
    /** Leeway for the browser's automatic submit to reach the server after the countdown ends. */
    private const SUBMIT_GRACE_SECONDS = 30;

    public function show(Quiz $quiz): View
    {
        abort_unless($quiz->isActive(), 404);
        $quiz->load('course')->loadCount(['questions as question_count' => fn ($query) => $query->active()]);
        $quiz->question_count = $quiz->attemptSize((int) $quiz->question_count);

        return view('public.quizzes.show', [
            'quiz' => $quiz,
            'title' => $quiz->title.' | '.config('app.name'),
            'canTake' => $canTake = $this->canTake($quiz),
            'resume' => $canTake && $this->openAttempt($quiz),
        ]);
    }

    public function start(Quiz $quiz): RedirectResponse
    {
        abort_unless($quiz->isActive(), 404);
        $this->guard($quiz);

        $open = $this->openAttempt($quiz);

        if (! $open) {
            $open = $quiz->attempts()->create([
                'user_id' => auth()->id(),
                'question_ids' => $quiz->drawQuestionIds(),
                'started_at' => now(),
                'elapsed_seconds' => 0,
            ]);
        }

        return redirect()->route('quizzes.attempt', $quiz);
    }

    public function attempt(Quiz $quiz): View|RedirectResponse
    {
        abort_unless($quiz->isActive(), 404);
        $this->guard($quiz);
        $attempt = $this->openAttempt($quiz);

        if (! $attempt) {
            return redirect()->route('quizzes.show', $quiz);
        }

        $quiz->load('course');
        $attempt->recordActivity();

        return view('public.quizzes.attempt', [
            'quiz' => $quiz,
            'attempt' => $attempt,
            'questions' => $quiz->questionsFor($attempt->question_ids),
            'remaining' => $attempt->remainingSeconds($quiz),
            'elapsed' => (int) $attempt->elapsed_seconds,
        ]);
    }

    public function heartbeat(Request $request, Quiz $quiz): JsonResponse
    {
        abort_unless($quiz->isActive(), 404);
        $this->guard($quiz);
        $attempt = $this->openAttempt($quiz);

        if (! $attempt) {
            return response()->json(['open' => false]);
        }

        $attempt->recordActivity(! $request->boolean('leaving'));

        return response()->json([
            'open' => true,
            'remaining' => $attempt->remainingSeconds($quiz),
            'elapsed' => (int) $attempt->elapsed_seconds,
        ]);
    }

    public function submit(Request $request, Quiz $quiz, QuizScorer $scorer): RedirectResponse
    {
        abort_unless($quiz->isActive(), 404);
        $this->guard($quiz);
        $attempt = $this->openAttempt($quiz);
        abort_unless($attempt, 404);

        $answers = $request->input('answers', []);
        $answers = is_array($answers) ? $answers : [];
        $result = $scorer->score($quiz, $answers, $attempt->question_ids);
        $attempt->recordActivity(false);
        $expired = $quiz->time_limit_minutes
            && $attempt->elapsed_seconds > $quiz->time_limit_minutes * 60 + self::SUBMIT_GRACE_SECONDS;

        $attempt->update([
            'score' => $result['score'],
            'max_score' => $result['max'],
            'percentage' => $result['percentage'],
            'passed' => $result['passed'] && ! $expired,
            'submitted_at' => now(),
        ]);

        foreach ($result['rows'] as $row) {
            $attempt->answers()->create([
                'quiz_question_id' => $row['question']->id,
                'selected_option_ids' => $row['selected'],
                'answer_text' => $row['answer_text'] ?? null,
                'is_correct' => $row['is_correct'],
                'marks_awarded' => $row['marks_awarded'],
            ]);
        }

        $message = match (true) {
            $expired => 'The time limit had passed, so this attempt is marked as not passed.',
            $request->boolean('timed_out') => 'Time is up. Your answers were submitted automatically.',
            default => 'Your result is ready.',
        };

        return redirect()
            ->route('quizzes.results', [$quiz, $attempt])
            ->with('status', $message);
    }

    public function results(Quiz $quiz, QuizAttempt $attempt): View
    {
        abort_unless($attempt->quiz_id === $quiz->id && $attempt->user_id === auth()->id(), 403);
        abort_unless($attempt->submitted_at, 404);
        $attempt->load('answers.question.options');

        return view('public.quizzes.results', [
            'quiz' => $quiz,
            'attempt' => $attempt,
            'title' => 'Result | '.$quiz->title,
        ]);
    }

    private function guard(Quiz $quiz): void
    {
        abort_unless($this->canTake($quiz), 403);
    }

    private function canTake(Quiz $quiz): bool
    {
        $user = auth()->user();

        if (! $user || ! $user->isActiveAccount() || $user->role->value !== 'student') {
            return false;
        }

        if (! $quiz->course_id) {
            return true;
        }

        return $user->can('viewContent', $quiz->course);
    }

    private function openAttempt(Quiz $quiz): ?QuizAttempt
    {
        return $quiz->attempts()
            ->where('user_id', auth()->id())
            ->whereNull('submitted_at')
            ->latest()
            ->first();
    }
}
