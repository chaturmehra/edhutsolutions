<?php

namespace App\Services\Quizzes;

use App\Models\Quiz;
use App\Models\QuizQuestion;
use App\Support\QuestionTypes;

class QuizScorer
{
    /**
     * @param  array<int|string, array<int, int|string>>  $answers
     * @param  array<int, int>|null  $questionIds  The attempt's drawn questions; null scores every active question.
     * @return array{score: float, max: float, percentage: float, passed: bool, rows: array<int, array<string, mixed>>}
     */
    public function score(Quiz $quiz, array $answers, ?array $questionIds = null): array
    {
        $score = 0.0;
        $max = 0.0;
        $rows = [];

        $questions = $quiz->questionsFor($questionIds);

        foreach ($questions as $question) {
            $raw = $answers[$question->id] ?? [];
            $written = $this->writtenAnswer($raw);

            if (QuestionTypes::isEssay($question->type)) {
                $rows[] = [
                    'question' => $question,
                    'selected' => [],
                    'correct' => [],
                    'answer_text' => $written,
                    'is_correct' => false,
                    'marks_awarded' => 0,
                ];
                continue;
            }

            $max += (float) $question->marks;
            $correct = [];

            if (QuestionTypes::showsAcceptedAnswers($question->type)) {
                $accepted = $question->options
                    ->where('is_correct', true)
                    ->pluck('option_text')
                    ->map(fn ($text) => $this->normalize((string) $text));
                $given = $this->normalize($written);
                $isCorrect = $given !== '' && $accepted->contains($given);
                $answered = $given !== '';
                $selected = [];
            } else {
                $selected = collect(is_array($raw) ? $raw : [$raw])
                    ->map(fn ($id) => (int) $id)
                    ->filter(fn ($id) => $id > 0)
                    ->unique()
                    ->sort()
                    ->values();
                $correct = $question->options
                    ->where('is_correct', true)
                    ->pluck('id')
                    ->map(fn ($id) => (int) $id)
                    ->sort()
                    ->values();
                $known = $question->options->pluck('id')->map(fn ($id) => (int) $id);
                $selected = $selected->intersect($known)->sort()->values();
                $isCorrect = $selected->all() === $correct->all() && $correct->isNotEmpty();
                $answered = $selected->isNotEmpty();
                $written = null;
            }

            $awarded = 0.0;
            if ($isCorrect) {
                $awarded = (float) $question->marks;
            } elseif ($answered) {
                $penalty = $question->negative_marks !== null
                    ? (float) $question->negative_marks
                    : (float) $quiz->negative_marking;
                $awarded = -1 * $penalty;
            }

            $score += $awarded;
            $rows[] = [
                'question' => $question,
                'selected' => is_array($selected) ? $selected : $selected->all(),
                'correct' => is_array($correct) ? $correct : $correct->all(),
                'answer_text' => $written,
                'is_correct' => $isCorrect,
                'marks_awarded' => $awarded,
            ];
        }

        $score = max(0, round($score, 2));
        $percentage = $max > 0 ? round($score / $max * 100, 2) : 0.0;

        return [
            'score' => $score,
            'max' => round($max, 2),
            'percentage' => $percentage,
            'passed' => $percentage >= (float) $quiz->pass_percentage,
            'rows' => $rows,
        ];
    }

    public function isMultiple(QuizQuestion $question): bool
    {
        return $question->type === 'multiple';
    }

    private function writtenAnswer(mixed $raw): string
    {
        if (is_array($raw)) {
            return trim((string) ($raw['text'] ?? ''));
        }

        return '';
    }

    private function normalize(string $value): string
    {
        $value = trim(preg_replace('/\s+/', ' ', $value) ?? '');

        return mb_strtolower($value);
    }
}
