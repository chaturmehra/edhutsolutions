<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Course;
use App\Models\CoursePdf;
use App\Models\CourseVideo;
use App\Models\Quiz;
use App\Models\QuizOption;
use App\Models\QuizQuestion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CourseMaterialTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_upload_course_pdf_video_quiz_and_questions(): void
    {
        Storage::fake('local');
        $admin = User::factory()->superAdmin()->create();
        $course = $this->course();

        $this->actingAs($admin)
            ->get(route('admin.courses.materials', $course->id))
            ->assertOk()
            ->assertSee('Quizzes')
            ->assertSee('PDFs')
            ->assertSee('Videos');

        $this->actingAs($admin)
            ->post(route('admin.courses.pdfs.store', $course->id), [
                'title' => 'Orientation notes',
                'file' => UploadedFile::fake()->create('notes.pdf', 120, 'application/pdf'),
                'sort_order' => 1,
                'status' => 'active',
            ])
            ->assertRedirect(route('admin.courses.materials', $course->id));

        $pdf = CoursePdf::query()->firstOrFail();
        $this->assertSame($course->id, $pdf->course_id);
        $this->assertSame('notes.pdf', $pdf->original_name);
        Storage::disk('local')->assertExists($pdf->file_path);

        $this->actingAs($admin)
            ->post(route('admin.courses.videos.store', $course->id), [
                'title' => 'Classroom recording',
                'source_type' => 'upload',
                'file' => UploadedFile::fake()->create('class.mp4', 200, 'video/mp4'),
                'status' => 'active',
            ])
            ->assertRedirect(route('admin.courses.materials', $course->id));

        $video = CourseVideo::query()->where('title', 'Classroom recording')->firstOrFail();
        $this->assertSame('upload', $video->source_type);
        Storage::disk('local')->assertExists($video->file_path);

        $this->actingAs($admin)
            ->post(route('admin.courses.videos.store', $course->id), [
                'title' => 'Published walkthrough',
                'source_type' => 'link',
                'external_url' => 'https://www.edhutsolutions.com',
                'status' => 'active',
            ])
            ->assertRedirect(route('admin.courses.materials', $course->id));

        $this->actingAs($admin)
            ->post(route('admin.courses.quizzes.store', $course->id), [
                'title' => 'Orientation check',
                'pass_percentage' => 70,
                'status' => 'active',
            ])
            ->assertRedirect();

        $quiz = Quiz::query()->where('title', 'Orientation check')->firstOrFail();
        $this->assertSame($course->id, $quiz->course_id);

        $this->actingAs($admin)
            ->post(route('admin.courses.questions.store', [$course->id, $quiz->id]), [
                'type' => 'single',
                'prompt' => 'How many questions does the published page list?',
                'marks' => 1,
                'status' => 'active',
                'options' => [
                    ['text' => '100'],
                    ['text' => '180', 'correct' => '1'],
                ],
            ])
            ->assertRedirect(route('admin.courses.questions', [$course->id, $quiz->id]));

        $this->actingAs($admin)
            ->post(route('admin.courses.questions.store', [$course->id, $quiz->id]), [
                'type' => 'multiple',
                'prompt' => 'Which domains are listed?',
                'marks' => 2,
                'status' => 'active',
                'options' => [
                    ['text' => 'People', 'correct' => '1'],
                    ['text' => 'Process', 'correct' => '1'],
                    ['text' => 'Only procurement'],
                ],
            ])
            ->assertRedirect(route('admin.courses.questions', [$course->id, $quiz->id]));

        $this->assertSame(2, QuizQuestion::query()->count());
        $this->assertSame(5, QuizOption::query()->count());
        $this->assertSame(1, QuizQuestion::query()->where('type', 'single')->firstOrFail()->options()->where('is_correct', true)->count());

        $this->actingAs($admin)
            ->from(route('admin.courses.questions.create', [$course->id, $quiz->id]))
            ->post(route('admin.courses.questions.store', [$course->id, $quiz->id]), [
                'type' => 'single',
                'prompt' => 'Two correct answers should be rejected.',
                'marks' => 1,
                'status' => 'active',
                'options' => [
                    ['text' => 'One', 'correct' => '1'],
                    ['text' => 'Two', 'correct' => '1'],
                ],
            ])
            ->assertRedirect(route('admin.courses.questions.create', [$course->id, $quiz->id]))
            ->assertSessionHasErrors('options');

        $this->actingAs($admin)
            ->delete(route('admin.courses.pdfs.destroy', [$course->id, $pdf->id]))
            ->assertRedirect(route('admin.courses.materials', $course->id));
        Storage::disk('local')->assertMissing($pdf->file_path);

        $this->actingAs(User::factory()->create())
            ->get(route('admin.courses.materials', $course->id))
            ->assertForbidden();
    }

    public function test_answer_type_shows_the_fields_that_question_needs(): void
    {
        $admin = User::factory()->superAdmin()->create();
        $course = $this->course();
        $quiz = Quiz::query()->create([
            'course_id' => $course->id,
            'title' => 'Type check',
            'slug' => 'type-check',
            'pass_percentage' => 70,
            'status' => 'active',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.courses.questions.create', [$course->id, $quiz->id]))
            ->assertOk()
            ->assertSee('Multiple Choice (MCQ)')
            ->assertSee('Multiple Answer')
            ->assertSee('True / False')
            ->assertSee('Fill in the Blank')
            ->assertSee('Short Answer')
            ->assertSee('Long Answer / Essay')
            ->assertSee('Scenario Based (MCQ)')
            ->assertSee('Answer options');

        $this->actingAs($admin)
            ->post(route('admin.courses.questions.store', [$course->id, $quiz->id]), [
                'type' => 'essay',
                'prompt' => 'Describe one risk response.',
                'marks' => 5,
                'status' => 'active',
            ])
            ->assertRedirect(route('admin.courses.questions', [$course->id, $quiz->id]));

        $this->actingAs($admin)
            ->from(route('admin.courses.questions.create', [$course->id, $quiz->id]))
            ->post(route('admin.courses.questions.store', [$course->id, $quiz->id]), [
                'type' => 'fill_blank',
                'prompt' => 'The published page lists ____ questions.',
                'marks' => 1,
                'status' => 'active',
            ])
            ->assertSessionHasErrors('options');

        $this->actingAs($admin)
            ->post(route('admin.courses.questions.store', [$course->id, $quiz->id]), [
                'type' => 'true_false',
                'prompt' => 'The published page lists 180 questions.',
                'marks' => 1,
                'status' => 'active',
                'options' => [
                    ['text' => 'True', 'correct' => '1'],
                    ['text' => 'False'],
                ],
            ])
            ->assertRedirect(route('admin.courses.questions', [$course->id, $quiz->id]));

        $this->actingAs($admin)
            ->from(route('admin.courses.questions.create', [$course->id, $quiz->id]))
            ->post(route('admin.courses.questions.store', [$course->id, $quiz->id]), [
                'type' => 'scenario',
                'prompt' => 'What should the manager do first?',
                'marks' => 1,
                'status' => 'active',
                'options' => [
                    ['text' => 'Identify the risk', 'correct' => '1'],
                    ['text' => 'Ignore the risk'],
                ],
            ])
            ->assertSessionHasErrors('scenario');
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
            'title' => 'PMP Certification Training',
            'slug' => 'pmp-certification-training',
            'status' => 'active',
            'price_on_request' => true,
        ]);
    }
}
