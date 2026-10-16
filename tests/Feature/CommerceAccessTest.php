<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Course;
use App\Models\CoursePdf;
use App\Models\CourseVideo;
use App\Models\Quiz;
use App\Models\Redirect;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CommerceAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cart_is_kept_after_login_and_checkout_waits_for_approval(): void
    {
        $course = $this->course();
        $student = User::factory()->create([
            'email' => 'learner@edhut.test',
        ]);

        $this->post(route('cart.store', $course))->assertRedirect();
        $this->get('/cart')->assertOk()->assertSee($course->title);
        $this->get('/checkout')->assertRedirect('/login');

        $this->post('/login', [
            'email' => 'learner@edhut.test',
            'password' => 'password',
        ])->assertRedirect('/checkout');

        $this->get('/cart')->assertSee($course->title);
        $this->checkout()->assertRedirect()->assertSessionHas('toast');

        $this->assertDatabaseHas('orders', [
            'user_id' => $student->id,
            'status' => 'pending',
        ]);
        $this->assertDatabaseCount('cart_items', 0);
        $this->assertDatabaseCount('enrollments', 0);
    }

    public function test_admin_approval_opens_the_course_and_rejection_does_not(): void
    {
        $course = $this->course();
        $student = User::factory()->create();
        $admin = User::factory()->superAdmin()->create();

        $this->actingAs($student)->post(route('cart.store', $course));
        $this->actingAs($student)->checkout();
        $orderId = $student->orders()->first()->id;

        $this->actingAs($student)->get(route('learn.show', $course))->assertForbidden();
        $this->actingAs($admin)->post(route('admin.orders.approve', $orderId), [
            'admin_note' => 'Seat confirmed.',
        ])->assertRedirect();

        $this->assertDatabaseHas('enrollments', [
            'user_id' => $student->id,
            'course_id' => $course->id,
            'status' => 'active',
        ]);
        $this->actingAs($student)->get(route('learn.show', $course))->assertOk()->assertSee($course->title);
        $this->actingAs($student)->get('/admin/orders/'.$orderId)->assertForbidden();
    }

    public function test_bought_course_shows_purchase_state_and_cannot_be_added_again(): void
    {
        $course = $this->course();
        $student = User::factory()->create();
        $admin = User::factory()->superAdmin()->create();

        $this->actingAs($student)->post(route('cart.store', $course));
        $this->actingAs($student)->checkout();
        $order = $student->orders()->first();

        $this->actingAs($student)->get(route('courses.show', $course))
            ->assertSee('Already purchased')
            ->assertSee(route('account.orders.show', $order), false)
            ->assertDontSee(route('cart.store', $course), false);
        $this->actingAs($student)->post(route('cart.store', $course))
            ->assertSessionHas('toast_ok', false);
        $this->assertDatabaseCount('cart_items', 0);

        $this->actingAs($admin)->get(route('admin.orders.index'))
            ->assertSee(route('admin.orders.show', $order), false)
            ->assertSee('View');
        $this->actingAs($admin)->post(route('admin.orders.approve', $order));

        $this->actingAs($student)->get(route('courses.show', $course))
            ->assertSee('Continue learning')
            ->assertSee(route('learn.show', $course), false);
        $this->actingAs($student)->get(route('courses.index'))
            ->assertSee('Continue learning');
    }

    public function test_pdf_is_private_until_the_enrolled_student_opens_it(): void
    {
        Storage::fake('local');
        Storage::disk('local')->put('notes/sample.pdf', '%PDF-1.4 sample');

        $course = $this->course();
        $pdf = CoursePdf::query()->create([
            'course_id' => $course->id,
            'title' => 'Sample notes',
            'file_path' => 'notes/sample.pdf',
            'original_name' => 'sample.pdf',
            'status' => 'active',
        ]);
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $admin = User::factory()->superAdmin()->create();

        $this->actingAs($owner)->post(route('cart.store', $course));
        $this->actingAs($owner)->checkout();
        $order = $owner->orders()->first();

        $this->actingAs($owner)->get(route('learn.pdf', $pdf))->assertForbidden();
        $this->actingAs($admin)->post(route('admin.orders.approve', $order));

        $this->actingAs($owner)->get(route('learn.pdf', $pdf))
            ->assertOk()
            ->assertSee('data-pdf-viewer', false)
            ->assertSee('data-watermark="EdHut Solutions · info@edhutsolutions.com"', false)
            ->assertSee(route('learn.pdf.file', $pdf), false)
            ->assertDontSee('%PDF-1.4');
        $this->actingAs($owner)->get(route('learn.pdf.file', $pdf), ['Sec-Fetch-Dest' => 'document'])->assertForbidden();
        $this->actingAs($owner)->get(route('learn.pdf.file', $pdf))->assertForbidden();
        $this->actingAs($owner)->get(route('learn.pdf.file', $pdf), ['X-Requested-With' => 'XMLHttpRequest', 'Sec-Fetch-Dest' => 'empty'])
            ->assertOk()
            ->assertHeader('Content-Type', 'application/pdf');
        $this->actingAs($other)->get(route('learn.pdf', $pdf))->assertForbidden();
        $this->actingAs($other)->get(route('learn.pdf.file', $pdf), ['X-Requested-With' => 'XMLHttpRequest'])->assertForbidden();
        auth()->logout();
        $this->get(route('learn.pdf', $pdf))->assertRedirect('/login');
        $this->assertDatabaseHas('media_access_logs', [
            'user_id' => $owner->id,
            'resource_type' => 'pdf',
            'resource_id' => $pdf->id,
        ]);
    }

    public function test_uploaded_video_only_streams_to_a_video_player(): void
    {
        Storage::fake('local');
        Storage::disk('local')->put('videos/intro.mp4', 'fake-mp4-bytes');

        $course = $this->course();
        $video = CourseVideo::query()->create([
            'course_id' => $course->id,
            'title' => 'Intro',
            'source_type' => 'upload',
            'file_path' => 'videos/intro.mp4',
            'status' => 'active',
        ]);
        $student = User::factory()->create();
        $student->enrollments()->create(['course_id' => $course->id, 'status' => 'active', 'enrolled_at' => now()]);

        $this->actingAs($student)->get(route('learn.show', $course))
            ->assertOk()
            ->assertSee('controlslist="nodownload', false)
            ->assertSee('EdHut Solutions · info@edhutsolutions.com')
            ->assertDontSee($student->email.' · '.$student->learnerCode());
        $this->actingAs($student)->get(route('learn.video', $video), ['Sec-Fetch-Dest' => 'document'])->assertForbidden();
        $this->actingAs($student)->get(route('learn.video', $video))->assertForbidden();
        $this->actingAs($student)->get(route('learn.video', $video), ['Sec-Fetch-Dest' => 'video'])
            ->assertOk()
            ->assertHeader('Content-Type', 'video/mp4');
    }

    public function test_quiz_scores_single_and_multiple_answers_for_an_enrolled_student(): void
    {
        $course = $this->course();
        $student = User::factory()->create();
        $student->enrollments()->create([
            'course_id' => $course->id,
            'status' => 'active',
            'enrolled_at' => now(),
        ]);
        $quiz = Quiz::query()->create([
            'course_id' => $course->id,
            'title' => 'Check',
            'slug' => 'check',
            'pass_percentage' => 70,
            'negative_marking' => 0,
            'status' => 'active',
        ]);
        $single = $quiz->questions()->create([
            'type' => 'single',
            'prompt' => 'Pick one',
            'marks' => 1,
            'negative_marks' => 0,
            'sort_order' => 1,
            'status' => 'active',
        ]);
        $right = $single->options()->create(['option_text' => 'Yes', 'is_correct' => true, 'sort_order' => 1]);
        $single->options()->create(['option_text' => 'No', 'is_correct' => false, 'sort_order' => 2]);
        $multi = $quiz->questions()->create([
            'type' => 'multiple',
            'prompt' => 'Pick both',
            'marks' => 2,
            'negative_marks' => 1,
            'sort_order' => 2,
            'status' => 'active',
        ]);
        $a = $multi->options()->create(['option_text' => 'A', 'is_correct' => true, 'sort_order' => 1]);
        $b = $multi->options()->create(['option_text' => 'B', 'is_correct' => true, 'sort_order' => 2]);
        $c = $multi->options()->create(['option_text' => 'C', 'is_correct' => false, 'sort_order' => 3]);

        $this->actingAs($student)->post(route('quizzes.start', $quiz))->assertRedirect(route('quizzes.attempt', $quiz));
        $this->actingAs($student)->post(route('quizzes.submit', $quiz), [
            'answers' => [
                $single->id => [$right->id],
                $multi->id => [$a->id, $b->id],
            ],
        ])->assertRedirect();

        $this->assertDatabaseHas('quiz_attempts', [
            'user_id' => $student->id,
            'quiz_id' => $quiz->id,
            'passed' => true,
        ]);

        $this->actingAs($student)->post(route('quizzes.start', $quiz));
        $this->actingAs($student)->post(route('quizzes.submit', $quiz), [
            'answers' => [
                $single->id => [$right->id],
                $multi->id => [$c->id],
            ],
        ]);
        $this->assertDatabaseHas('quiz_attempt_answers', [
            'quiz_question_id' => $multi->id,
            'is_correct' => false,
        ]);

        $stranger = User::factory()->create();
        $this->actingAs($stranger)->post(route('quizzes.start', $quiz))->assertForbidden();

        $attempt = $quiz->attempts()->whereNotNull('submitted_at')->first();
        $this->actingAs($student)->get(route('quizzes.results', [$quiz, $attempt]))
            ->assertOk()
            ->assertSee('qr-card is-correct', false)
            ->assertSee('Multiple Choice (MCQ) · 1 mark', false);

        $admin = User::factory()->superAdmin()->create();
        $this->actingAs($admin)->get(route('admin.quiz-results.index', ['result' => 'passed']))
            ->assertOk()
            ->assertSee($student->email)
            ->assertSee(route('admin.quiz-results.show', $attempt), false);
        $this->actingAs($admin)->get(route('admin.quiz-results.show', $attempt))
            ->assertOk()
            ->assertSee('Pick both')
            ->assertSee("Learner's answer")
            ->assertSee('quiz-review.css', false);
        $this->actingAs($student)->get(route('admin.quiz-results.index'))->assertForbidden();
    }

    public function test_timed_quiz_runs_in_steps_and_auto_submit_is_scored(): void
    {
        $this->freezeSecond();
        $course = $this->course();
        $student = User::factory()->create();
        $student->enrollments()->create(['course_id' => $course->id, 'status' => 'active', 'enrolled_at' => now()]);
        $quiz = Quiz::query()->create([
            'course_id' => $course->id,
            'title' => 'Timed check',
            'slug' => 'timed-check',
            'pass_percentage' => 50,
            'negative_marking' => 0,
            'time_limit_minutes' => 10,
            'status' => 'active',
        ]);
        $question = $quiz->questions()->create([
            'type' => 'mcq',
            'prompt' => 'Pick one',
            'marks' => 1,
            'negative_marks' => 0,
            'sort_order' => 1,
            'status' => 'active',
        ]);
        $right = $question->options()->create(['option_text' => 'Yes', 'is_correct' => true, 'sort_order' => 1]);

        $this->actingAs($student)->post(route('quizzes.start', $quiz));
        $this->actingAs($student)->get(route('quizzes.attempt', $quiz))
            ->assertOk()
            ->assertSee('learn-side', false)
            ->assertSee('data-quiz-step', false)
            ->assertSee('data-remaining="600"', false)
            ->assertSee('Question <span data-quiz-current>1</span> of 1', false);

        $quiz->attempts()->first()->update(['elapsed_seconds' => 605, 'last_seen_at' => null]);
        $this->actingAs($student)->post(route('quizzes.submit', $quiz), [
            'timed_out' => 1,
            'answers' => [$question->id => [$right->id]],
        ])->assertSessionHas('status', 'Time is up. Your answers were submitted automatically.');
        $this->assertDatabaseHas('quiz_attempts', ['quiz_id' => $quiz->id, 'passed' => true]);

        $this->actingAs($student)->post(route('quizzes.start', $quiz));
        $quiz->attempts()->whereNull('submitted_at')->first()->update(['elapsed_seconds' => 900, 'last_seen_at' => null]);
        $this->actingAs($student)->get(route('quizzes.attempt', $quiz))->assertSee('data-remaining="0"', false);
        $this->actingAs($student)->post(route('quizzes.submit', $quiz), [
            'answers' => [$question->id => [$right->id]],
        ])->assertSessionHas('status', 'The time limit had passed, so this attempt is marked as not passed.');
    }

    public function test_sitemap_lists_courses_and_a_redirect_sends_visitors_on(): void
    {
        $course = $this->course();
        $seo = User::factory()->seoManager()->create();

        $this->get('/sitemap.xml')->assertOk()->assertSee('/courses/'.$course->slug, false);
        $this->get('/gallery')->assertRedirect('/services/corporate-training');
        $this->get('/robots.txt')->assertOk()->assertSee('Sitemap:', false);

        $this->actingAs($seo)->post(route('admin.seo.redirects.store'), [
            'from_path' => '/old-course',
            'to_path' => '/courses/'.$course->slug,
            'status_code' => 301,
        ])->assertSessionHasNoErrors()->assertRedirect();

        $this->get('/old-course')->assertRedirect('/courses/'.$course->slug);
        $this->assertTrue(Redirect::query()->where('from_path', '/old-course')->exists());
    }

    private function checkout(): \Illuminate\Testing\TestResponse
    {
        return $this->post('/checkout', [
            'name' => 'Learner',
            'phone' => '9876543210',
            'country' => 'IN',
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
            'short_description' => 'PMP preparation.',
            'status' => 'active',
            'price' => 0,
            'price_on_request' => true,
        ]);
    }
}
