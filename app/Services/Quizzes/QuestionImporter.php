<?php

namespace App\Services\Quizzes;

use App\Models\Quiz;
use App\Support\QuestionTypes;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class QuestionImporter
{
    public const MAX_ROWS = 1000;

    public const MAX_OPTIONS = 6;

    private const SHOWN_ERRORS = 15;

    /**
     * @return array<int, string>
     */
    public static function headers(): array
    {
        $options = array_map(fn ($n) => 'option_'.$n, range(1, self::MAX_OPTIONS));

        return ['type', 'question', 'scenario', ...$options, 'correct', 'marks', 'negative_marks', 'explanation', 'status'];
    }

    public function template(): string
    {
        $rows = [
            self::headers(),
            ['mcq', 'Which document formally authorizes a project?', '', 'Project charter', 'Scope statement', 'Risk register', 'Stakeholder register', '', '', 'A', '1', '0', 'The project charter formally authorizes the project.', 'active'],
            ['multiple', 'Which of these are project constraints?', '', 'Scope', 'Schedule', 'Cost', 'Team lunch', '', '', 'A,B,C', '2', '0', '', 'active'],
            ['true_false', 'A project is a temporary endeavour.', '', '', '', '', '', '', '', 'True', '1', '0', '', 'active'],
            ['scenario', 'What should the project manager do first?', 'A key stakeholder asks for a new feature halfway through the project.', 'Add the feature', 'Assess the impact of the change', 'Refuse the request', 'Escalate to the sponsor', '', '', 'B', '1', '0', '', 'active'],
            ['fill_blank', 'The ____ path is the longest path through the network.', '', 'critical', '', '', '', '', '', '', '1', '0', 'Every option is an accepted answer.', 'active'],
            ['essay', 'Describe how you would handle a conflict between two team members.', '', '', '', '', '', '', '', '', '5', '0', 'Reviewed manually.', 'active'],
        ];

        $handle = fopen('php://temp', 'r+');
        fwrite($handle, "\xEF\xBB\xBF");
        foreach ($rows as $row) {
            fputcsv($handle, $row);
        }
        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return $csv;
    }

    /**
     * Adds every row as a question, or none of them if any row is invalid.
     *
     * @throws ValidationException
     */
    public function import(Quiz $quiz, string $path): int
    {
        $rows = $this->rows($path);
        $questions = [];
        $errors = [];

        foreach ($rows as $line => $row) {
            $parsed = $this->parse($row);
            if (is_string($parsed)) {
                $errors[] = 'Row '.$line.': '.$parsed;

                continue;
            }
            $questions[] = $parsed;
        }

        if ($errors !== []) {
            $shown = array_slice($errors, 0, self::SHOWN_ERRORS);
            if (count($errors) > self::SHOWN_ERRORS) {
                $shown[] = (count($errors) - self::SHOWN_ERRORS).' more rows have problems.';
            }
            throw ValidationException::withMessages(['file' => $shown]);
        }

        DB::transaction(function () use ($quiz, $questions) {
            $order = (int) $quiz->questions()->max('sort_order');
            foreach ($questions as [$data, $options]) {
                $question = $quiz->questions()->create($data + ['sort_order' => ++$order]);
                foreach ($options as $index => $option) {
                    $question->options()->create([
                        'option_text' => $option['text'],
                        'is_correct' => $option['correct'],
                        'sort_order' => $index + 1,
                    ]);
                }
            }
        });

        return count($questions);
    }

    /**
     * @return array<int, array<string, string>> Rows keyed by spreadsheet line number.
     *
     * @throws ValidationException
     */
    private function rows(string $path): array
    {
        $contents = (string) file_get_contents($path);
        $contents = preg_replace('/^\xEF\xBB\xBF/', '', $contents);
        if (! mb_check_encoding($contents, 'UTF-8')) {
            $contents = mb_convert_encoding($contents, 'UTF-8', 'Windows-1252');
        }

        $handle = fopen('php://temp', 'r+');
        fwrite($handle, $contents);
        rewind($handle);

        $first = strtok($contents, "\r\n") ?: '';
        $delimiter = substr_count($first, ';') > substr_count($first, ',') ? ';' : ',';

        $header = fgetcsv($handle, null, $delimiter, '"', '');
        $columns = array_map(fn ($name) => $this->column((string) $name), $header ?: []);
        if (! in_array('question', $columns, true)) {
            fclose($handle);
            throw ValidationException::withMessages(['file' => 'The first row must be the column headings from the template, including "question".']);
        }

        $rows = [];
        $line = 1;
        while (($cells = fgetcsv($handle, null, $delimiter, '"', '')) !== false) {
            $line++;
            if ($cells === [null] || implode('', array_map('trim', array_map('strval', $cells))) === '') {
                continue;
            }
            if (count($rows) >= self::MAX_ROWS) {
                fclose($handle);
                throw ValidationException::withMessages(['file' => 'Upload at most '.self::MAX_ROWS.' questions at a time.']);
            }
            $row = [];
            foreach ($columns as $index => $column) {
                if ($column !== '') {
                    $row[$column] = trim((string) ($cells[$index] ?? ''));
                }
            }
            $rows[$line] = $row;
        }
        fclose($handle);

        if ($rows === []) {
            throw ValidationException::withMessages(['file' => 'The file has no questions below the heading row.']);
        }

        return $rows;
    }

    private function column(string $name): string
    {
        $name = trim(mb_strtolower($name));
        $name = preg_replace('/[^a-z0-9]+/', '_', $name);
        $name = trim($name, '_');

        if (preg_match('/^option_?([a-f1-6])$/', $name, $match)) {
            $index = ctype_digit($match[1]) ? (int) $match[1] : ord($match[1]) - 96;

            return 'option_'.$index;
        }

        return match ($name) {
            'prompt', 'question_text' => 'question',
            'question_type' => 'type',
            'correct_answer', 'correct_answers', 'answer', 'answers' => 'correct',
            'negative_mark', 'negative_marking' => 'negative_marks',
            'mark' => 'marks',
            default => in_array($name, self::headers(), true) ? $name : '',
        };
    }

    /**
     * @param  array<string, string>  $row
     * @return array{0: array<string, mixed>, 1: array<int, array{text: string, correct: bool}>}|string
     */
    private function parse(array $row): array|string
    {
        $type = $this->type($row['type'] ?? '');
        if ($type === null) {
            return 'unknown type "'.$row['type'].'". Use one of: '.implode(', ', QuestionTypes::keys()).'.';
        }

        $prompt = $row['question'] ?? '';
        if ($prompt === '') {
            return 'the question is empty.';
        }
        if (mb_strlen($prompt) > 5000 || mb_strlen($row['scenario'] ?? '') > 5000 || mb_strlen($row['explanation'] ?? '') > 5000) {
            return 'the question, scenario and explanation must each be 5000 characters or fewer.';
        }

        $marks = ($row['marks'] ?? '') === '' ? '1' : $row['marks'];
        $negative = ($row['negative_marks'] ?? '') === '' ? '0' : $row['negative_marks'];
        if (! is_numeric($marks) || (float) $marks < 0 || ! is_numeric($negative) || (float) $negative < 0) {
            return 'marks and negative_marks must be numbers of zero or more.';
        }

        $status = mb_strtolower($row['status'] ?? '') ?: 'active';
        if (! in_array($status, ['active', 'inactive'], true)) {
            return 'status must be active or inactive.';
        }

        $options = $this->options($type, $row);
        if (is_string($options)) {
            return $options;
        }

        $scenario = QuestionTypes::isScenario($type) ? (($row['scenario'] ?? '') ?: null) : null;
        $problems = QuestionTypes::problems($type, $scenario, $options);
        if ($problems !== []) {
            return implode(' ', $problems);
        }

        return [[
            'type' => $type,
            'prompt' => $prompt,
            'scenario' => $scenario,
            'marks' => $marks,
            'negative_marks' => $negative,
            'explanation' => ($row['explanation'] ?? '') ?: null,
            'status' => $status,
        ], $options];
    }

    private function type(string $value): ?string
    {
        $value = trim(mb_strtolower($value));
        if ($value === '') {
            return 'mcq';
        }
        $key = trim(preg_replace('/[^a-z]+/', '_', $value), '_');
        $aliases = [
            'single' => 'single', 'mcq' => 'mcq', 'multiple_choice' => 'mcq', 'single_choice' => 'mcq',
            'multiple' => 'multiple', 'multiple_answer' => 'multiple', 'multi' => 'multiple',
            'true_false' => 'true_false', 'truefalse' => 'true_false', 'tf' => 'true_false',
            'fill_blank' => 'fill_blank', 'fill_in_the_blank' => 'fill_blank', 'blank' => 'fill_blank',
            'short' => 'short', 'short_answer' => 'short',
            'essay' => 'essay', 'long_answer' => 'essay', 'long_answer_essay' => 'essay',
            'scenario' => 'scenario', 'scenario_based' => 'scenario', 'scenario_based_mcq' => 'scenario',
        ];

        return $aliases[$key] ?? null;
    }

    /**
     * @param  array<string, string>  $row
     * @return array<int, array{text: string, correct: bool}>|string
     */
    private function options(string $type, array $row): array|string
    {
        if (QuestionTypes::isEssay($type)) {
            return [];
        }

        $correct = mb_strtolower($row['correct'] ?? '');

        if (QuestionTypes::isTrueFalse($type)) {
            $answer = match ($correct) {
                'true', 't', 'a', '1', 'yes' => 'true',
                'false', 'f', 'b', '2', 'no' => 'false',
                default => null,
            };
            if ($answer === null) {
                return 'set correct to True or False.';
            }

            return [
                ['text' => 'True', 'correct' => $answer === 'true'],
                ['text' => 'False', 'correct' => $answer === 'false'],
            ];
        }

        $texts = [];
        for ($n = 1; $n <= self::MAX_OPTIONS; $n++) {
            $text = $row['option_'.$n] ?? '';
            if ($text !== '') {
                if (mb_strlen($text) > 1000) {
                    return 'option_'.$n.' must be 1000 characters or fewer.';
                }
                $texts[$n] = $text;
            }
        }

        if (QuestionTypes::showsAcceptedAnswers($type)) {
            return array_values(array_map(fn ($text) => ['text' => $text, 'correct' => true], $texts));
        }

        $marked = [];
        foreach (preg_split('/[\s,|;]+/', $correct, -1, PREG_SPLIT_NO_EMPTY) as $token) {
            $n = ctype_digit($token) ? (int) $token : (preg_match('/^[a-f]$/', $token) ? ord($token) - 96 : 0);
            if (! isset($texts[$n])) {
                return 'correct "'.$row['correct'].'" does not point to a filled option. Use letters (A, B) or numbers (1, 2).';
            }
            $marked[$n] = true;
        }

        $options = [];
        foreach ($texts as $n => $text) {
            $options[] = ['text' => $text, 'correct' => isset($marked[$n])];
        }

        return $options;
    }
}
