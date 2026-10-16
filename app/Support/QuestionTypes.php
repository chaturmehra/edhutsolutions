<?php

namespace App\Support;

class QuestionTypes
{
    /**
     * @return array<string, string>
     */
    public static function labels(): array
    {
        return [
            'mcq' => 'Multiple Choice (MCQ)',
            'multiple' => 'Multiple Answer',
            'true_false' => 'True / False',
            'fill_blank' => 'Fill in the Blank',
            'short' => 'Short Answer',
            'essay' => 'Long Answer / Essay',
            'scenario' => 'Scenario Based (MCQ)',
        ];
    }

    /**
     * @return array<int, string>
     */
    public static function keys(): array
    {
        return ['single', ...array_keys(self::labels())];
    }

    public static function label(string $type): string
    {
        if ($type === 'single') {
            return self::labels()['mcq'];
        }

        return self::labels()[$type] ?? 'Question';
    }

    public static function showsOptions(string $type): bool
    {
        return ! self::isEssay($type);
    }

    public static function showsChoiceOptions(string $type): bool
    {
        return in_array($type, ['single', 'mcq', 'multiple', 'true_false', 'scenario'], true);
    }

    public static function showsAcceptedAnswers(string $type): bool
    {
        return in_array($type, ['fill_blank', 'short'], true);
    }

    public static function isEssay(string $type): bool
    {
        return $type === 'essay';
    }

    public static function isScenario(string $type): bool
    {
        return $type === 'scenario';
    }

    public static function isTrueFalse(string $type): bool
    {
        return $type === 'true_false';
    }

    public static function isSingleChoice(string $type): bool
    {
        return in_array($type, ['single', 'mcq', 'scenario', 'true_false'], true);
    }

    public static function isChoice(string $type): bool
    {
        return self::showsChoiceOptions($type);
    }

    /**
     * Problems with a question's scenario and options, keyed by form field.
     *
     * @param  iterable<int, array{text: string, correct: bool}>  $options
     * @return array<string, string>
     */
    public static function problems(string $type, ?string $scenario, iterable $options): array
    {
        $options = collect($options);
        $problems = [];

        if (self::isScenario($type) && ! filled($scenario)) {
            $problems['scenario'] = 'Add the scenario for this question.';
        }
        if (self::isEssay($type)) {
            return $problems;
        }
        if (self::showsAcceptedAnswers($type)) {
            if ($options->count() < 1) {
                $problems['options'] = 'Add at least one accepted answer.';
            }

            return $problems;
        }
        if (self::isTrueFalse($type)) {
            $labels = $options->map(fn ($option) => mb_strtolower($option['text']))->sort()->values()->all();
            if ($labels !== ['false', 'true'] || $options->where('correct', true)->count() !== 1) {
                $problems['options'] = 'Mark either True or False as the correct answer.';
            }

            return $problems;
        }

        $correct = $options->where('correct', true)->count();
        if ($options->count() < 2) {
            $problems['options'] = 'Add at least two answer options.';
        } elseif (self::isSingleChoice($type) && $correct !== 1) {
            $problems['options'] = 'Mark exactly one correct option.';
        } elseif ($type === 'multiple' && $correct < 1) {
            $problems['options'] = 'Mark every correct option. At least one is required.';
        }

        return $problems;
    }

    public static function instruction(string $type): string
    {
        return match ($type) {
            'multiple' => 'Select every correct option',
            'true_false' => 'Choose True or False',
            'fill_blank' => 'Type the missing word or phrase',
            'short' => 'Write a short answer',
            'essay' => 'Write your answer. This question is not marked automatically.',
            default => 'Select one option',
        };
    }
}
