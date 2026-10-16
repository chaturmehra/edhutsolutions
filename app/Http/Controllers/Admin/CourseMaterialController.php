<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\HandlesStudyMedia;
use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CoursePackage;
use App\Models\CoursePdf;
use App\Models\CourseSchedule;
use App\Models\CourseVideo;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use App\Models\Trainer;
use App\Services\Quizzes\QuestionImporter;
use App\Support\QuestionTypes;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CourseMaterialController extends Controller
{
    use HandlesStudyMedia;

    public function show(int $course): View
    {
        $course = $this->course($course);
        $course->load([
            'packages',
            'schedules.trainer',
            'sections' => fn ($query) => $query->withCount(['lessons' => fn ($lessons) => $lessons->active()])->orderBy('id'),
            'resources',
            'pdfs' => fn ($query) => $query->orderBy('sort_order')->orderBy('id'),
            'videos' => fn ($query) => $query->orderBy('sort_order')->orderBy('id'),
            'quizzes' => fn ($query) => $query->withCount('questions')->orderBy('title'),
        ]);

        return view('admin.courses.materials', ['course' => $course]);
    }

    public function createPackage(int $course): View
    {
        $course = $this->course($course);

        return view('admin.courses.package-form', [
            'course' => $course,
            'package' => null,
            'nextOrder' => (int) $course->packages()->max('sort_order') + 1,
        ]);
    }

    public function storePackage(Request $request, int $course): RedirectResponse
    {
        $course = $this->course($course);
        $course->packages()->create($this->packageData($request, $course));

        return redirect()->route('admin.courses.materials', $course->id)->with('status', 'Package saved.');
    }

    public function editPackage(int $course, int $package): View
    {
        $course = $this->course($course);

        return view('admin.courses.package-form', [
            'course' => $course,
            'package' => $this->package($course, $package),
            'nextOrder' => 1,
        ]);
    }

    public function updatePackage(Request $request, int $course, int $package): RedirectResponse
    {
        $course = $this->course($course);
        $this->package($course, $package)->update($this->packageData($request, $course));

        return redirect()->route('admin.courses.materials', $course->id)->with('status', 'Package updated.');
    }

    public function destroyPackage(int $course, int $package): RedirectResponse
    {
        $course = $this->course($course);
        $this->package($course, $package)->delete();

        return redirect()->route('admin.courses.materials', $course->id)->with('status', 'Package removed.');
    }

    public function createCohort(int $course): View
    {
        return view('admin.courses.cohort-form', [
            'course' => $this->course($course),
            'cohort' => null,
            'trainers' => $this->trainerOptions(),
        ]);
    }

    public function storeCohort(Request $request, int $course): RedirectResponse
    {
        $course = $this->course($course);
        $course->schedules()->create($this->cohortData($request));

        return redirect()->route('admin.courses.materials', $course->id)->with('status', 'Cohort saved.');
    }

    public function editCohort(int $course, int $cohort): View
    {
        $course = $this->course($course);

        return view('admin.courses.cohort-form', [
            'course' => $course,
            'cohort' => $this->cohort($course, $cohort),
            'trainers' => $this->trainerOptions(),
        ]);
    }

    public function updateCohort(Request $request, int $course, int $cohort): RedirectResponse
    {
        $course = $this->course($course);
        $this->cohort($course, $cohort)->update($this->cohortData($request));

        return redirect()->route('admin.courses.materials', $course->id)->with('status', 'Cohort updated.');
    }

    public function destroyCohort(int $course, int $cohort): RedirectResponse
    {
        $course = $this->course($course);
        $this->cohort($course, $cohort)->delete();

        return redirect()->route('admin.courses.materials', $course->id)->with('status', 'Cohort removed.');
    }

    public function createPdf(int $course): View
    {
        $course = $this->course($course);

        return view('admin.courses.pdf-form', [
            'course' => $course,
            'pdf' => null,
            'nextOrder' => (int) $course->pdfs()->max('sort_order') + 1,
        ]);
    }

    public function storePdf(Request $request, int $course): RedirectResponse
    {
        $course = $this->course($course);
        $data = $this->pdfData($request, true);
        $course->pdfs()->create([...$data, ...$this->storedPdf($request->file('file'), 'courses/pdfs')]);

        return redirect()->route('admin.courses.materials', $course->id)->with('status', 'PDF uploaded.');
    }

    public function editPdf(int $course, int $pdf): View
    {
        $course = $this->course($course);

        return view('admin.courses.pdf-form', [
            'course' => $course,
            'pdf' => $this->pdf($course, $pdf),
            'nextOrder' => 1,
        ]);
    }

    public function updatePdf(Request $request, int $course, int $pdf): RedirectResponse
    {
        $course = $this->course($course);
        $pdf = $this->pdf($course, $pdf);
        $data = $this->pdfData($request, false);
        if ($request->hasFile('file')) {
            $this->deletePrivate($pdf->file_path);
            $data = [...$data, ...$this->storedPdf($request->file('file'), 'courses/pdfs')];
        }
        $pdf->update($data);

        return redirect()->route('admin.courses.materials', $course->id)->with('status', 'PDF updated.');
    }

    public function destroyPdf(int $course, int $pdf): RedirectResponse
    {
        $course = $this->course($course);
        $pdf = $this->pdf($course, $pdf);
        $this->deletePrivate($pdf->file_path);
        $pdf->delete();

        return redirect()->route('admin.courses.materials', $course->id)->with('status', 'PDF removed.');
    }

    public function createVideo(int $course): View
    {
        $course = $this->course($course);

        return view('admin.courses.video-form', [
            'course' => $course,
            'video' => null,
            'nextOrder' => (int) $course->videos()->max('sort_order') + 1,
        ]);
    }

    public function storeVideo(Request $request, int $course): RedirectResponse
    {
        $course = $this->course($course);
        $course->videos()->create($this->videoPayload($request, null, 'courses/videos'));

        return redirect()->route('admin.courses.materials', $course->id)->with('status', 'Video saved.');
    }

    public function editVideo(int $course, int $video): View
    {
        $course = $this->course($course);

        return view('admin.courses.video-form', [
            'course' => $course,
            'video' => $this->video($course, $video),
            'nextOrder' => 1,
        ]);
    }

    public function updateVideo(Request $request, int $course, int $video): RedirectResponse
    {
        $course = $this->course($course);
        $video = $this->video($course, $video);
        $video->update($this->videoPayload($request, $video, 'courses/videos'));

        return redirect()->route('admin.courses.materials', $course->id)->with('status', 'Video updated.');
    }

    public function destroyVideo(int $course, int $video): RedirectResponse
    {
        $course = $this->course($course);
        $video = $this->video($course, $video);
        $this->deletePrivate($video->file_path);
        $video->delete();

        return redirect()->route('admin.courses.materials', $course->id)->with('status', 'Video removed.');
    }

    public function createQuiz(int $course): View
    {
        return view('admin.courses.quiz-form', [
            'course' => $this->course($course),
            'quiz' => null,
        ]);
    }

    public function storeQuiz(Request $request, int $course): RedirectResponse
    {
        $course = $this->course($course);
        $quiz = $course->quizzes()->create($this->quizData($request));

        return redirect()->route('admin.courses.questions', [$course->id, $quiz->id])
            ->with('status', 'Quiz saved. Add the questions next.');
    }

    public function editQuiz(int $course, int $quiz): View
    {
        $course = $this->course($course);

        return view('admin.courses.quiz-form', [
            'course' => $course,
            'quiz' => $this->quiz($course, $quiz),
        ]);
    }

    public function updateQuiz(Request $request, int $course, int $quiz): RedirectResponse
    {
        $course = $this->course($course);
        $quiz = $this->quiz($course, $quiz);
        $quiz->update($this->quizData($request, $quiz));

        return redirect()->route('admin.courses.questions', [$course->id, $quiz->id])->with('status', 'Quiz updated.');
    }

    public function destroyQuiz(int $course, int $quiz): RedirectResponse
    {
        $course = $this->course($course);
        $this->quiz($course, $quiz)->delete();

        return redirect()->route('admin.courses.materials', $course->id)->with('status', 'Quiz removed.');
    }

    public function questions(int $course, int $quiz): View
    {
        $course = $this->course($course);
        $quiz = $this->quiz($course, $quiz);
        $quiz->load(['questions.options']);

        return view('admin.courses.questions', [
            'course' => $course,
            'quiz' => $quiz,
        ]);
    }

    public function createQuestion(int $course, int $quiz): View
    {
        $course = $this->course($course);
        $quiz = $this->quiz($course, $quiz);

        return view('admin.courses.question-form', [
            'course' => $course,
            'quiz' => $quiz,
            'question' => null,
            'nextOrder' => (int) $quiz->questions()->max('sort_order') + 1,
        ]);
    }

    public function storeQuestion(Request $request, int $course, int $quiz): RedirectResponse
    {
        $course = $this->course($course);
        $quiz = $this->quiz($course, $quiz);
        [$data, $options] = $this->questionData($request);
        $question = $quiz->questions()->create($data);
        $this->syncOptions($question, $options);

        return redirect()->route('admin.courses.questions', [$course->id, $quiz->id])->with('status', 'Question saved.');
    }

    public function importQuestionsForm(int $course, int $quiz): View
    {
        $course = $this->course($course);

        return view('admin.courses.question-import', [
            'course' => $course,
            'quiz' => $this->quiz($course, $quiz),
            'headers' => QuestionImporter::headers(),
            'maxRows' => QuestionImporter::MAX_ROWS,
        ]);
    }

    public function importQuestions(Request $request, QuestionImporter $importer, int $course, int $quiz): RedirectResponse
    {
        $course = $this->course($course);
        $quiz = $this->quiz($course, $quiz);
        $request->validate([
            'file' => ['required', 'file', 'extensions:csv,txt', 'max:5120'],
        ], [
            'file.extensions' => 'Upload a CSV file. In Excel, use File > Save As > CSV UTF-8.',
        ]);

        $count = $importer->import($quiz, $request->file('file')->getRealPath());

        return redirect()->route('admin.courses.questions', [$course->id, $quiz->id])
            ->with('status', $count === 1 ? '1 question imported.' : $count.' questions imported.');
    }

    public function questionTemplate(QuestionImporter $importer, int $course, int $quiz): Response
    {
        $course = $this->course($course);
        $quiz = $this->quiz($course, $quiz);

        return response($importer->template(), 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$quiz->slug.'-questions-template.csv"',
        ]);
    }

    public function editQuestion(int $course, int $quiz, int $question): View
    {
        $course = $this->course($course);
        $quiz = $this->quiz($course, $quiz);

        return view('admin.courses.question-form', [
            'course' => $course,
            'quiz' => $quiz,
            'question' => $this->question($quiz, $question),
            'nextOrder' => 1,
        ]);
    }

    public function updateQuestion(Request $request, int $course, int $quiz, int $question): RedirectResponse
    {
        $course = $this->course($course);
        $quiz = $this->quiz($course, $quiz);
        $question = $this->question($quiz, $question);
        [$data, $options] = $this->questionData($request);
        $question->update($data);
        $this->syncOptions($question, $options);

        return redirect()->route('admin.courses.questions', [$course->id, $quiz->id])->with('status', 'Question updated.');
    }

    public function destroyQuestion(int $course, int $quiz, int $question): RedirectResponse
    {
        $course = $this->course($course);
        $quiz = $this->quiz($course, $quiz);
        $this->question($quiz, $question)->delete();

        return redirect()->route('admin.courses.questions', [$course->id, $quiz->id])->with('status', 'Question removed.');
    }

    private function course(int $course): Course
    {
        return Course::query()->findOrFail($course);
    }

    private function package(Course $course, int $package): CoursePackage
    {
        return $course->packages()->findOrFail($package);
    }

    private function cohort(Course $course, int $cohort): CourseSchedule
    {
        return $course->schedules()->findOrFail($cohort);
    }

    /**
     * @return Collection<int, Trainer>
     */
    private function trainerOptions(): Collection
    {
        return Trainer::query()->orderBy('sort_order')->orderBy('name')->get(['id', 'name', 'status']);
    }

    private function pdf(Course $course, int $pdf): CoursePdf
    {
        return $course->pdfs()->findOrFail($pdf);
    }

    private function video(Course $course, int $video): CourseVideo
    {
        return $course->videos()->findOrFail($video);
    }

    private function quiz(Course $course, int $quiz): Quiz
    {
        return $course->quizzes()->findOrFail($quiz);
    }

    private function question(Quiz $quiz, int $question): QuizQuestion
    {
        return $quiz->questions()->with('options')->findOrFail($question);
    }

    /**
     * @return array<string, mixed>
     */
    private function packageData(Request $request, Course $course): array
    {
        $this->blankToNull($request, ['compare_price', 'includes_label', 'features', 'sort_order']);
        $data = $request->validate([
            'name' => ['required', 'string', 'max:80'],
            'price' => ['required', 'numeric', 'min:0', 'max:99999999'],
            'compare_price' => ['nullable', 'numeric', 'min:0', 'max:99999999', 'gte:price'],
            'includes_label' => ['nullable', 'string', 'max:255'],
            'features' => ['nullable', 'string', 'max:10000'],
            'is_popular' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ], [
            'compare_price.gte' => 'The original price must be the same as or higher than the package price.',
        ]);

        return [
            'name' => $data['name'],
            'price' => $data['price'],
            'compare_price' => $data['compare_price'] ?? null,
            'currency' => $course->currency ?: 'INR',
            'includes_label' => $data['includes_label'] ?? null,
            'features' => isset($data['features']) ? trim($data['features']) : null,
            'is_popular' => $request->boolean('is_popular'),
            'sort_order' => (int) ($data['sort_order'] ?? 0),
            'status' => $data['status'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function cohortData(Request $request): array
    {
        $this->blankToNull($request, ['ends_on', 'time_label', 'hours_label', 'delivery_mode', 'trainer_id']);
        $data = $request->validate([
            'starts_on' => ['required', 'date'],
            'ends_on' => ['nullable', 'date', 'after_or_equal:starts_on'],
            'time_label' => ['nullable', 'string', 'max:120'],
            'hours_label' => ['nullable', 'string', 'max:120'],
            'delivery_mode' => ['nullable', 'string', 'max:120'],
            'trainer_id' => ['nullable', 'integer', Rule::exists('trainers', 'id')->whereNull('deleted_at')],
            'session_dates' => ['nullable', 'array', 'max:60'],
            'session_dates.*' => ['nullable', 'date'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ], [
            'ends_on.after_or_equal' => 'The end date must be on or after the start date.',
        ]);

        $sessions = collect($data['session_dates'] ?? [])
            ->filter()
            ->map(fn ($date) => Carbon::parse($date)->toDateString())
            ->unique()
            ->sort()
            ->values()
            ->all();

        return [
            'starts_on' => $data['starts_on'],
            'ends_on' => $data['ends_on'] ?? null,
            'time_label' => $data['time_label'] ?? null,
            'hours_label' => $data['hours_label'] ?? null,
            'delivery_mode' => $data['delivery_mode'] ?? null,
            'trainer_id' => $data['trainer_id'] ?? null,
            'session_dates' => $sessions === [] ? null : $sessions,
            'status' => $data['status'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function quizData(Request $request, ?Quiz $quiz = null): array
    {
        $this->blankToNull($request, ['slug', 'time_limit_minutes', 'negative_marking', 'questions_per_attempt']);
        if ($request->filled('slug')) {
            $request->merge(['slug' => Str::slug((string) $request->input('slug')) ?: null]);
        }
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('quizzes', 'slug')->ignore($quiz?->id)],
            'description' => ['nullable', 'string'],
            'kind' => ['nullable', Rule::in(array_keys(Quiz::KINDS))],
            'pass_percentage' => ['required', 'numeric', 'min:0', 'max:100'],
            'negative_marking' => ['nullable', 'numeric', 'min:0'],
            'time_limit_minutes' => ['nullable', 'integer', 'min:1'],
            'questions_per_attempt' => ['nullable', 'integer', 'min:1', 'max:1000'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);

        $slug = filled($data['slug'] ?? null) ? Str::slug($data['slug']) : $this->uniqueSlug($data['title'], $quiz?->id);

        return [
            'title' => $data['title'],
            'slug' => $slug !== '' ? $slug : $this->uniqueSlug($data['title'], $quiz?->id),
            'description' => $data['description'] ?? null,
            'kind' => $data['kind'] ?? 'practice',
            'pass_percentage' => $data['pass_percentage'],
            'negative_marking' => $data['negative_marking'] ?? 0,
            'time_limit_minutes' => $data['time_limit_minutes'] ?? null,
            'questions_per_attempt' => $data['questions_per_attempt'] ?? null,
            'status' => $data['status'],
        ];
    }

    /**
     * @return array{0: array<string, mixed>, 1: array<int, array{id: int|null, text: string, correct: bool}>}
     */
    private function questionData(Request $request): array
    {
        $this->blankToNull($request, ['negative_marks', 'sort_order', 'explanation', 'scenario']);
        $type = (string) $request->input('type');
        $validator = Validator::make($request->all(), [
            'type' => ['required', Rule::in(QuestionTypes::keys())],
            'prompt' => ['required', 'string', 'max:5000'],
            'scenario' => ['nullable', 'string', 'max:5000'],
            'marks' => ['required', 'numeric', 'min:0'],
            'negative_marks' => ['nullable', 'numeric', 'min:0'],
            'explanation' => ['nullable', 'string', 'max:5000'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'options' => ['nullable', 'array', 'max:12'],
            'options.*.id' => ['nullable', 'integer'],
            'options.*.text' => ['nullable', 'string', 'max:1000'],
            'options.*.correct' => ['nullable', 'boolean'],
        ]);

        $options = collect($request->input('options', []))
            ->filter(fn ($option) => is_array($option) && filled($option['text'] ?? null))
            ->map(fn ($option) => [
                'id' => filled($option['id'] ?? null) ? (int) $option['id'] : null,
                'text' => trim((string) $option['text']),
                'correct' => QuestionTypes::showsAcceptedAnswers($type)
                    || filter_var($option['correct'] ?? false, FILTER_VALIDATE_BOOLEAN),
            ])
            ->values();

        $validator->after(function ($validator) use ($request, $options, $type) {
            foreach (QuestionTypes::problems($type, $request->input('scenario'), $options) as $field => $message) {
                $validator->errors()->add($field, $message);
            }
        });
        $validator->validate();

        return [[
            'type' => $type,
            'prompt' => $request->input('prompt'),
            'scenario' => QuestionTypes::isScenario($type) ? $request->input('scenario') : null,
            'marks' => $request->input('marks'),
            'negative_marks' => $request->input('negative_marks'),
            'explanation' => $request->input('explanation'),
            'sort_order' => (int) ($request->input('sort_order') ?? 0),
            'status' => $request->input('status'),
        ], QuestionTypes::isEssay($type) ? [] : $options->all()];
    }

    /**
     * @param  array<int, array{id: int|null, text: string, correct: bool}>  $options
     */
    private function syncOptions(QuizQuestion $question, array $options): void
    {
        $kept = [];
        foreach ($options as $index => $option) {
            $payload = [
                'option_text' => $option['text'],
                'is_correct' => $option['correct'],
                'sort_order' => $index + 1,
            ];
            $existing = $option['id'] ? $question->options()->whereKey($option['id'])->first() : null;
            if ($existing) {
                $existing->update($payload);
                $kept[] = $existing->id;
                continue;
            }
            $kept[] = $question->options()->create($payload)->id;
        }
        $question->options()->whereNotIn('id', $kept)->delete();
    }

    private function uniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title) ?: 'quiz';
        $slug = $base;
        $suffix = 2;
        while (Quiz::withTrashed()->where('slug', $slug)->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))->exists()) {
            $slug = $base.'-'.$suffix;
            $suffix++;
        }

        return $slug;
    }
}
