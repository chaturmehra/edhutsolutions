<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Course;
use App\Models\CoursePdf;
use App\Models\CourseVideo;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\QuizQuestion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class MockTestAndImportTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_bulk_upload_questions_from_csv(): void
    {
        $admin = User::factory()->superAdmin()->create();
        $quiz = $this->quiz($this->course());
        $quiz->questions()->create(['type' => 'essay', 'prompt' => 'Existing question', 'marks' => 1, 'sort_order' => 4, 'status' => 'active']);

        $this->actingAs($admin)
            ->get(route('admin.courses.questions', [$quiz->course_id, $quiz->id]))
            ->assertOk()
            ->assertSee(route('admin.courses.questions.import.form', [$quiz->course_id, $quiz->id]), false);
        $this->actingAs($admin)
            ->get(route('admin.courses.questions.import.form', [$quiz->course_id, $quiz->id]))
            ->assertOk()
            ->assertSee('Download CSV template');

        $template = $this->actingAs($admin)->get(route('admin.courses.questions.template', [$quiz->course_id, $quiz->id]));
        $template->assertOk()->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        $this->assertStringContainsString('type,question,scenario,option_1', $template->getContent());

        $csv = "\xEF\xBB\xBF".implode("\n", [
            'Type,Question,Scenario,Option 1,Option 2,Option 3,Option 4,Correct,Marks,Negative Marks,Explanation,Status',
            'mcq,Which document authorizes a project?,,Charter,Scope statement,Risk register,,a,1,0.25,The charter does.,active',
            'multiple,Pick the constraints,,Scope,Schedule,Lunch,,"A, B",2,,,',
            'true_false,A project is temporary.,,,,,,True,,,,',
            'scenario,What first?,A stakeholder wants a change.,Add it,Assess impact,,,2,,,,inactive',
            'fill_blank,The ____ path is longest.,,critical,Critical Path,,,,,,,',
            'essay,Describe a conflict.,,,,,,,5,,,',
            ',,,,,,,,,,,',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.courses.questions.import', [$quiz->course_id, $quiz->id]), [
                'file' => UploadedFile::fake()->createWithContent('questions.csv', $csv),
            ])
            ->assertRedirect(route('admin.courses.questions', [$quiz->course_id, $quiz->id]))
            ->assertSessionHas('status', '6 questions imported.');

        $this->assertSame(7, $quiz->questions()->count());
        $mcq = QuizQuestion::query()->where('prompt', 'Which document authorizes a project?')->firstOrFail();
        $this->assertSame(5, $mcq->sort_order);
        $this->assertSame('0.25', (string) $mcq->negative_marks);
        $this->assertSame(['Charter'], $mcq->options()->where('is_correct', true)->pluck('option_text')->all());

        $multiple = QuizQuestion::query()->where('type', 'multiple')->firstOrFail();
        $this->assertSame(['Scope', 'Schedule'], $multiple->options()->where('is_correct', true)->pluck('option_text')->all());
        $this->assertSame(['True'], QuizQuestion::query()->where('type', 'true_false')->firstOrFail()->options()->where('is_correct', true)->pluck('option_text')->all());

        $scenario = QuizQuestion::query()->where('type', 'scenario')->firstOrFail();
        $this->assertSame('inactive', $scenario->status);
        $this->assertSame('A stakeholder wants a change.', $scenario->scenario);
        $this->assertSame(2, QuizQuestion::query()->where('type', 'fill_blank')->firstOrFail()->options()->where('is_correct', true)->count());
        $this->assertSame(0, QuizQuestion::query()->where('prompt', 'Describe a conflict.')->firstOrFail()->options()->count());
    }

    public function test_bulk_upload_rejects_the_whole_file_when_a_row_is_invalid(): void
    {
        $admin = User::factory()->superAdmin()->create();
        $quiz = $this->quiz($this->course());

        $csv = implode("\n", [
            'type;question;option_1;option_2;correct',
            'mcq;Fine row;Yes;No;A',
            'mcq;Two answers;Yes;No;A,B',
            'mcq;Points nowhere;Yes;No;D',
            'riddle;Unknown type;Yes;No;A',
        ]);

        $this->actingAs($admin)
            ->from(route('admin.courses.questions.import.form', [$quiz->course_id, $quiz->id]))
            ->post(route('admin.courses.questions.import', [$quiz->course_id, $quiz->id]), [
                'file' => UploadedFile::fake()->createWithContent('questions.csv', $csv),
            ])
            ->assertRedirect(route('admin.courses.questions.import.form', [$quiz->course_id, $quiz->id]))
            ->assertSessionHasErrors('file');

        $errors = session('errors')->get('file');
        $this->assertCount(3, $errors);
        $this->assertStringStartsWith('Row 3: Mark exactly one correct option.', $errors[0]);
        $this->assertStringStartsWith('Row 4:', $errors[1]);
        $this->assertStringStartsWith('Row 5: unknown type', $errors[2]);
        $this->assertSame(0, $quiz->questions()->count());

        $this->actingAs($admin)
            ->post(route('admin.courses.questions.import', [$quiz->course_id, $quiz->id]), [
                'file' => UploadedFile::fake()->create('questions.xlsx', 10),
            ])
            ->assertSessionHasErrors('file');
    }

    public function test_mock_test_draws_a_random_set_for_every_attempt(): void
    {
        $course = $this->course();
        $student = $this->student($course);
        $quiz = $this->quiz($course, ['kind' => 'mock', 'questions_per_attempt' => 3]);
        $ids = [];
        foreach (range(1, 8) as $n) {
            $question = $quiz->questions()->create(['type' => 'mcq', 'prompt' => 'Question number '.$n, 'marks' => 1, 'sort_order' => $n, 'status' => 'active']);
            $question->options()->create(['option_text' => 'Right', 'is_correct' => true, 'sort_order' => 1]);
            $question->options()->create(['option_text' => 'Wrong', 'is_correct' => false, 'sort_order' => 2]);
            $ids[] = $question->id;
        }
        $quiz->questions()->create(['type' => 'mcq', 'prompt' => 'Hidden draft', 'marks' => 1, 'sort_order' => 9, 'status' => 'inactive']);

        $this->actingAs($student)->get(route('quizzes.show', $quiz))->assertOk();
        $this->actingAs($student)->post(route('quizzes.start', $quiz))->assertRedirect(route('quizzes.attempt', $quiz));
        $first = QuizAttempt::query()->latest('id')->firstOrFail();
        $this->assertCount(3, $first->question_ids);
        $this->assertSame([], array_diff($first->question_ids, $ids));

        $this->actingAs($student)->post(route('quizzes.start', $quiz));
        $this->assertSame(1, QuizAttempt::query()->count(), 'An open attempt resumes with the same questions.');

        $page = $this->actingAs($student)->get(route('quizzes.attempt', $quiz))->assertOk();
        $drawn = QuizQuestion::query()->findMany($first->question_ids);
        foreach ($drawn as $question) {
            $page->assertSee($question->prompt);
        }
        $page->assertDontSee('Hidden draft')->assertSee('PMP mock test');
        $this->assertSame(3, substr_count($page->getContent(), 'data-quiz-step>'));

        $answers = $drawn->mapWithKeys(fn (QuizQuestion $question) => [$question->id => [$question->options()->where('is_correct', true)->value('id')]])->all();
        $this->actingAs($student)->post(route('quizzes.submit', $quiz), ['answers' => $answers])->assertRedirect();
        $first->refresh();
        $this->assertSame('3.00', (string) $first->max_score);
        $this->assertTrue($first->passed);
        $this->assertSame(3, $first->answers()->count());

        $sets = [];
        foreach (range(1, 6) as $round) {
            $this->actingAs($student)->post(route('quizzes.start', $quiz));
            $attempt = QuizAttempt::query()->latest('id')->firstOrFail();
            $sets[] = implode(',', $attempt->question_ids);
            $this->actingAs($student)->post(route('quizzes.submit', $quiz), ['answers' => []]);
        }
        $this->assertSame(7, QuizAttempt::query()->count());
        $this->assertGreaterThan(1, count(array_unique([implode(',', $first->question_ids), ...$sets])));
    }

    public function test_practice_test_keeps_every_question_in_order(): void
    {
        $course = $this->course();
        $student = $this->student($course);
        $quiz = $this->quiz($course);
        foreach (range(1, 4) as $n) {
            $quiz->questions()->create(['type' => 'essay', 'prompt' => 'Practice '.$n, 'marks' => 1, 'sort_order' => $n, 'status' => 'active']);
        }

        $this->actingAs($student)->post(route('quizzes.start', $quiz));
        $this->assertNull(QuizAttempt::query()->firstOrFail()->question_ids);
        $this->actingAs($student)->get(route('quizzes.attempt', $quiz))
            ->assertSeeInOrder(['Practice 1', 'Practice 2', 'Practice 3', 'Practice 4']);
    }

    public function test_learn_page_shows_material_and_tests_under_tabs(): void
    {
        $course = $this->course();
        $course->update(['overview' => "First overview paragraph.\n\nSecond overview paragraph."]);
        $student = $this->student($course);
        CoursePdf::query()->create([
            'course_id' => $course->id,
            'title' => 'Exam content outline',
            'description' => 'Domains and tasks in the current outline.',
            'file_path' => 'courses/pdfs/outline.pdf',
            'status' => 'active',
        ]);
        $mock = $this->quiz($course, ['title' => 'Full mock exam', 'slug' => 'full-mock', 'kind' => 'mock', 'questions_per_attempt' => 2, 'time_limit_minutes' => 230]);
        $practice = $this->quiz($course, ['title' => 'Domain practice', 'slug' => 'domain-practice']);
        $this->quiz($course, ['title' => 'Empty practice', 'slug' => 'empty-practice']);
        foreach ([$mock, $mock, $mock, $practice] as $n => $quiz) {
            $quiz->questions()->create(['type' => 'essay', 'prompt' => 'Q'.$n, 'marks' => 1, 'sort_order' => $n, 'status' => 'active']);
        }

        $page = $this->actingAs($student)->get(route('learn.show', $course))->assertOk();
        $page->assertSeeInOrder(['>Overview<', '>Study Material<', '>Mock Test<', '>Practice Test<'], false)
            ->assertDontSee('id="learn-tab-video"', false)
            ->assertSee('Second overview paragraph.')
            ->assertSee('Domains and tasks in the current outline.')
            ->assertSee(route('learn.pdf', CoursePdf::query()->firstOrFail()), false)
            ->assertSeeInOrder(['Full mock exam', '230', '2', 'Start'])
            ->assertSee(route('quizzes.start', $practice), false)
            ->assertDontSee('Empty practice');

        CourseVideo::query()->create([
            'course_id' => $course->id,
            'title' => 'Recorded class',
            'source_type' => 'link',
            'external_url' => 'https://www.edhutsolutions.com',
            'status' => 'active',
        ]);
        $this->actingAs($student)->post(route('quizzes.start', $mock));
        $this->actingAs($student)->get(route('learn.show', $course))
            ->assertSeeInOrder(['>Practice Test<', '>Video<'], false)
            ->assertSee('Recorded class')
            ->assertSee('Resume');
    }

    private function student(Course $course): User
    {
        $student = User::factory()->create();
        $student->enrollments()->create(['course_id' => $course->id, 'status' => 'active', 'enrolled_at' => now()]);

        return $student;
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function quiz(Course $course, array $attributes = []): Quiz
    {
        return Quiz::query()->create($attributes + [
            'course_id' => $course->id,
            'title' => 'Practice set',
            'slug' => 'practice-set',
            'kind' => 'practice',
            'pass_percentage' => 50,
            'negative_marking' => 0,
            'status' => 'active',
        ]);
    }

    private function course(): Course
    {
        $category = Category::query()->create([
            'name' => 'Project Management',
            'slug' => 'project-management',
            'status' => 'active',
        ]);

        return Course::query()->create([
            'category_id' => $category->id,
            'title' => 'Project Management Professional (PMP)',
            'slug' => 'pmp-certification-training',
            'status' => 'active',
            'price' => 0,
        ]);
    }
}
