<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Course;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuizPauseAndRevealTest extends TestCase
{
    use RefreshDatabase;

    public function test_clock_stops_while_the_learner_is_away_from_the_test(): void
    {
        $this->freezeSecond();
        [$student, $quiz] = $this->setUpQuiz(['time_limit_minutes' => 10]);

        $this->actingAs($student)->post(route('quizzes.start', $quiz));
        $this->actingAs($student)->get(route('quizzes.attempt', $quiz))
            ->assertSee('data-remaining="600"', false)
            ->assertSee(route('quizzes.heartbeat', $quiz), false);

        $this->travel(20)->seconds();
        $this->actingAs($student)->postJson(route('quizzes.heartbeat', $quiz))
            ->assertOk()
            ->assertJson(['open' => true, 'remaining' => 580, 'elapsed' => 20]);

        $this->travel(10)->seconds();
        $this->actingAs($student)->postJson(route('quizzes.heartbeat', $quiz), ['leaving' => 1])
            ->assertJson(['remaining' => 570]);
        $this->assertNull(QuizAttempt::query()->firstOrFail()->last_seen_at);

        $this->travel(3)->hours();
        $this->actingAs($student)->get(route('quizzes.attempt', $quiz))
            ->assertSee('data-remaining="570"', false);

        $this->travel(5)->minutes();
        $this->actingAs($student)->get(route('quizzes.attempt', $quiz))
            ->assertSee('data-remaining="'.(570 - QuizAttempt::MAX_SILENCE_SECONDS).'"', false);

        $this->travel(2)->days();
        $this->actingAs($student)->post(route('quizzes.submit', $quiz), ['answers' => []])
            ->assertSessionHas('status', 'Your result is ready.');
        $attempt = QuizAttempt::query()->firstOrFail();
        $this->assertSame(30 + 2 * QuizAttempt::MAX_SILENCE_SECONDS, $attempt->timeSpentSeconds());

        $this->actingAs($student)->postJson(route('quizzes.heartbeat', $quiz))->assertJson(['open' => false]);
    }

    public function test_untimed_test_counts_active_time_spent(): void
    {
        $this->freezeSecond();
        [$student, $quiz] = $this->setUpQuiz();

        $this->actingAs($student)->post(route('quizzes.start', $quiz));
        $this->actingAs($student)->get(route('quizzes.attempt', $quiz))->assertSee('data-elapsed="0"', false);
        $this->travel(45)->seconds();
        $this->actingAs($student)->postJson(route('quizzes.heartbeat', $quiz), ['leaving' => 1])
            ->assertJson(['remaining' => null, 'elapsed' => 45]);
        $this->travel(1)->hours();
        $this->actingAs($student)->get(route('quizzes.attempt', $quiz))->assertSee('data-elapsed="45"', false);
    }

    public function test_practice_test_offers_show_answer_but_mock_test_does_not(): void
    {
        [$student, $practice] = $this->setUpQuiz();

        $this->actingAs($student)->post(route('quizzes.start', $practice));
        $this->actingAs($student)->get(route('quizzes.attempt', $practice))
            ->assertOk()
            ->assertSee('Show answer')
            ->assertSee('data-quiz-correct', false)
            ->assertSee('Correct answer')
            ->assertSee('Charter authorizes the project.')
            ->assertSee('Accepted answers')
            ->assertSee('critical · critical path');

        $mock = Quiz::query()->create([
            'course_id' => $practice->course_id,
            'title' => 'Mock',
            'slug' => 'mock',
            'kind' => 'mock',
            'pass_percentage' => 50,
            'status' => 'active',
        ]);
        foreach ($practice->questions as $question) {
            $copy = $mock->questions()->create($question->only(['type', 'prompt', 'marks', 'explanation', 'sort_order', 'status']));
            foreach ($question->options as $option) {
                $copy->options()->create($option->only(['option_text', 'is_correct', 'sort_order']));
            }
        }

        $this->actingAs($student)->post(route('quizzes.start', $mock));
        $this->actingAs($student)->get(route('quizzes.attempt', $mock))
            ->assertOk()
            ->assertSee('Which document authorizes a project?')
            ->assertDontSee('Show answer')
            ->assertDontSee('data-quiz-correct', false)
            ->assertDontSee('Charter authorizes the project.');
    }

    /**
     * @param  array<string, mixed>  $attributes
     * @return array{0: User, 1: Quiz}
     */
    private function setUpQuiz(array $attributes = []): array
    {
        $category = Category::query()->create(['name' => 'Project Management', 'slug' => 'project-management', 'status' => 'active']);
        $course = Course::query()->create([
            'category_id' => $category->id,
            'title' => 'Project Management Professional (PMP)',
            'slug' => 'pmp-certification-training',
            'status' => 'active',
            'price' => 0,
        ]);
        $student = User::factory()->create();
        $student->enrollments()->create(['course_id' => $course->id, 'status' => 'active', 'enrolled_at' => now()]);

        $quiz = Quiz::query()->create($attributes + [
            'course_id' => $course->id,
            'title' => 'Practice',
            'slug' => 'practice',
            'kind' => 'practice',
            'pass_percentage' => 50,
            'status' => 'active',
        ]);
        $choice = $quiz->questions()->create([
            'type' => 'mcq',
            'prompt' => 'Which document authorizes a project?',
            'marks' => 1,
            'explanation' => 'Charter authorizes the project.',
            'sort_order' => 1,
            'status' => 'active',
        ]);
        $choice->options()->create(['option_text' => 'Charter', 'is_correct' => true, 'sort_order' => 1]);
        $choice->options()->create(['option_text' => 'Risk register', 'is_correct' => false, 'sort_order' => 2]);
        $blank = $quiz->questions()->create(['type' => 'fill_blank', 'prompt' => 'The ____ path is longest.', 'marks' => 1, 'sort_order' => 2, 'status' => 'active']);
        $blank->options()->create(['option_text' => 'critical', 'is_correct' => true, 'sort_order' => 1]);
        $blank->options()->create(['option_text' => 'critical path', 'is_correct' => true, 'sort_order' => 2]);

        return [$student, $quiz->fresh()];
    }
}
