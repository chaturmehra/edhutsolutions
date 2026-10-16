<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Course;
use App\Models\Service;
use App\Models\Testimonial;
use App\Models\Trainer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourseShowcaseTest extends TestCase
{
    use RefreshDatabase;

    public function test_course_page_shows_services_roadmap_and_only_its_own_reviews(): void
    {
        $pmp = $this->course('pmp-certification-training', 'Project Management Professional (PMP)');
        $pgmp = $this->course('pgmp-certification-training', 'Program Management Professional (PgMP)');

        $service = Service::query()->create(['title' => 'Exam Simulation Tool', 'slug' => 'exam-simulation-tool', 'short_description' => 'Timed mock exams.', 'status' => 'active']);
        $plan = $service->plans()->create(['label' => 'PMP', 'price' => 6999, 'currency' => 'INR', 'status' => 'active']);

        Testimonial::query()->create(['course_id' => $pmp->id, 'name' => 'Pmp Learner', 'headline' => 'Great PMP prep', 'quote' => 'PMP quote.', 'rating' => 5, 'status' => 'active']);
        Testimonial::query()->create(['course_id' => $pmp->id, 'name' => 'Hidden Learner', 'headline' => 'Draft', 'quote' => 'Inactive quote.', 'rating' => 5, 'status' => 'inactive']);
        Testimonial::query()->create(['course_id' => $pgmp->id, 'name' => 'Pgmp Learner', 'headline' => 'Great PgMP prep', 'quote' => 'PgMP quote.', 'rating' => 5, 'status' => 'active']);
        Testimonial::query()->create(['name' => 'General Learner', 'headline' => 'Good training', 'quote' => 'General quote.', 'rating' => 5, 'status' => 'active']);

        $this->get(route('courses.show', $pmp))
            ->assertOk()
            ->assertSeeInOrder(['Success in their words', 'Our services', 'Certification roadmap'])
            ->assertSee('Exam Simulation Tool')
            ->assertSee(route('cart.plans.store', $plan->id), false)
            ->assertSee('href="'.route('courses.show', $pgmp).'"', false)
            ->assertSee('aria-current="page"', false)
            ->assertSee('Voices from our PMP alumni.')
            ->assertSee('Pmp Learner')
            ->assertDontSee('Hidden Learner')
            ->assertDontSee('Pgmp Learner')
            ->assertDontSee('General Learner');

        $this->get(route('courses.show', $pgmp))
            ->assertSee('Pgmp Learner')
            ->assertDontSee('Pmp Learner');

        $this->get(route('home'))
            ->assertSee('Voices from our alumni.')
            ->assertSee('General Learner');
    }

    public function test_course_faqs_follow_the_alumni_reviews(): void
    {
        $course = $this->course('pmp-certification-training', 'Project Management Professional (PMP)');
        $course->faqs()->create(['question' => 'What is the PMP certification?', 'answer' => 'A PMI credential.', 'status' => 'active']);
        $course->faqs()->create(['question' => 'Hidden question?', 'answer' => 'Draft.', 'status' => 'inactive']);
        Testimonial::query()->create(['course_id' => $course->id, 'name' => 'Pmp Learner', 'headline' => 'Great', 'quote' => 'PMP quote.', 'rating' => 5, 'status' => 'active']);

        $this->get(route('courses.show', $course))
            ->assertOk()
            ->assertSeeInOrder(['Success in their words', 'PMP® FAQs', 'Your most asked questions.', 'What is the PMP certification?', 'Ready to prepare for a PMI credential?'])
            ->assertDontSee('Hidden question?');
    }

    public function test_quick_overview_reads_the_course_copy_from_the_content_file(): void
    {
        $pmp = $this->course('pmp-certification-training', 'Project Management Professional (PMP)');
        $other = $this->course('custom-course', 'Custom Course');

        $this->get(route('courses.show', $pmp))
            ->assertOk()
            ->assertSeeInOrder(['Quick overview', 'Eligibility', '35 hours of project management education', 'Who should pursue', 'Career benefits', 'Training methodology', '7 full-length mock exams'])
            ->assertSee('data-overview-card', false);

        $this->get(route('courses.show', $pmp))
            ->assertSeeInOrder(['Certification journey map', 'PMP® certification success', '01', 'Authorized training', 'Application approval assistance', '7 full-length mock exams', '05', 'Earn your certification', 'One journey. Five milestones.', 'Quick overview']);

        $this->get(route('courses.show', $pmp))
            ->assertSeeInOrder(['Knowledge capsules', 'Quick facts every PMP® candidate should know.', '180 questions', 'Why PMP® with EdHut', '7 full-length mock exams', 'Certification roadmap']);

        $this->get(route('courses.show', $other))
            ->assertOk()
            ->assertDontSee('Certification journey map')
            ->assertDontSee('Quick overview')
            ->assertDontSee('Knowledge capsules')
            ->assertDontSee('with EdHut');
    }

    public function test_hall_of_fame_shows_the_course_alumni_added_in_admin(): void
    {
        $pmp = $this->course('pmp-certification-training', 'Project Management Professional (PMP)');
        $pgmp = $this->course('pgmp-certification-training', 'Program Management Professional (PgMP)');
        $admin = User::factory()->superAdmin()->create();

        $this->get(route('courses.show', $pmp))->assertDontSee('Hall of fame');

        foreach ([['Meera Shah', $pmp, 'Senior Project Manager', 'active'], ['Karan Gill', $pgmp, 'Program Director', 'active'], ['Draft Person', $pmp, null, 'inactive']] as [$name, $course, $role, $status]) {
            $this->actingAs($admin)->post(route('admin.modules.store', 'hall-of-fame'), [
                'course_id' => $course->id,
                'name' => $name,
                'job_title' => $role,
                'status' => $status,
            ])->assertSessionHasNoErrors();
        }

        $this->actingAs($admin)->get(route('admin.modules.index', 'hall-of-fame'))
            ->assertOk()
            ->assertSee('Meera Shah')
            ->assertSee('Program Management Professional (PgMP)');
        auth()->logout();

        $this->get(route('courses.show', $pmp))
            ->assertOk()
            ->assertSeeInOrder(['Hall of fame', 'Meet our PMP® certified professionals.', '6,000+', 'PMP® certified alumni', '25,000+', 'Meera Shah', 'Senior Project Manager', 'PMP® Certified', 'Certification journey map'])
            ->assertSee('data-shot-label="Show certified professional"', false)
            ->assertDontSee('Karan Gill')
            ->assertDontSee('Draft Person');

        $this->get(route('courses.show', $pgmp))
            ->assertSee('Karan Gill')
            ->assertSee('PgMP® Certified')
            ->assertDontSee('Meera Shah');
    }

    public function test_admin_assigns_trainers_who_show_on_their_course_pages(): void
    {
        $pmp = $this->course('pmp-certification-training', 'Project Management Professional (PMP)');
        $pgmp = $this->course('pgmp-certification-training', 'Program Management Professional (PgMP)');
        $admin = User::factory()->superAdmin()->create();

        $this->actingAs($admin)->post(route('admin.modules.store', 'trainers'), [
            'name' => 'Asha Mentor',
            'credentials' => 'PMP, PgMP',
            'experience' => '20+ Years Experience',
            'bio' => 'Leads program management sessions.',
            'linkedin_url' => 'https://www.linkedin.com/in/asha-mentor',
            'courses' => [$pmp->id, $pgmp->id],
            'status' => 'active',
        ])->assertSessionHasNoErrors();

        $this->actingAs($admin)->post(route('admin.modules.store', 'trainers'), [
            'name' => 'Ravi Risk',
            'courses' => [$pgmp->id],
            'status' => 'active',
        ])->assertSessionHasNoErrors();

        $this->actingAs($admin)->post(route('admin.modules.store', 'trainers'), [
            'name' => 'Hidden Trainer',
            'courses' => [$pmp->id],
            'status' => 'inactive',
        ])->assertSessionHasNoErrors();

        $asha = Trainer::query()->where('name', 'Asha Mentor')->firstOrFail();
        $this->assertEqualsCanonicalizing([$pmp->id, $pgmp->id], $asha->courses()->pluck('courses.id')->all());

        $this->actingAs($admin)->get(route('admin.modules.create', 'trainers'))
            ->assertOk()
            ->assertSee('Courses they teach')
            ->assertSee('name="courses[]" value="'.$pmp->id.'"', false);
        $this->actingAs($admin)->get(route('admin.modules.edit', ['trainers', $asha->id]))
            ->assertOk()
            ->assertSee('name="courses[]" value="'.$pgmp->id.'" checked', false);

        auth()->logout();
        $this->get(route('courses.show', $pmp))
            ->assertSeeInOrder(['Our trainers', 'Meet the trainers who teach PMP®.', 'Asha Mentor', 'PMP, PgMP', '20+ Years Experience', 'linkedin.com/in/asha-mentor'], false)
            ->assertDontSee('Ravi Risk')
            ->assertDontSee('Hidden Trainer');

        $this->actingAs($admin)->put(route('admin.modules.update', ['trainers', $asha->id]), [
            'name' => 'Asha Mentor',
            'courses' => [$pgmp->id],
            'status' => 'active',
        ])->assertSessionHasNoErrors();

        auth()->logout();
        $this->get(route('courses.show', $pmp))
            ->assertDontSee('Our trainers')
            ->assertDontSee('Asha Mentor');
        $this->get(route('courses.show', $pgmp))
            ->assertSee('Asha Mentor')
            ->assertSee('Ravi Risk');
    }

    public function test_course_page_hides_reviews_when_none_are_linked(): void
    {
        $course = $this->course('pfmp-certification-training', 'Portfolio Management Professional (PfMP)');

        $this->get(route('courses.show', $course))
            ->assertOk()
            ->assertSee('Certification roadmap')
            ->assertDontSee('Success in their words');
    }

    public function test_admin_links_a_testimonial_to_a_course(): void
    {
        $course = $this->course('pmp-certification-training', 'Project Management Professional (PMP)');
        $admin = User::factory()->superAdmin()->create();

        $this->actingAs($admin)->post(route('admin.modules.store', 'testimonials'), [
            'course_id' => $course->id,
            'name' => 'Linked Learner',
            'quote' => 'Linked quote.',
            'status' => 'active',
        ])->assertSessionHasNoErrors();

        $this->actingAs($admin)->post(route('admin.modules.store', 'testimonials'), [
            'course_id' => '',
            'name' => 'General Learner',
            'quote' => 'General quote.',
            'status' => 'active',
        ])->assertSessionHasNoErrors();

        $this->assertDatabaseHas('testimonials', ['name' => 'Linked Learner', 'course_id' => $course->id]);
        $this->assertDatabaseHas('testimonials', ['name' => 'General Learner', 'course_id' => null]);
    }

    private function course(string $slug, string $title): Course
    {
        $category = Category::query()->firstOrCreate(
            ['slug' => 'project-management'],
            ['name' => 'Project Management', 'status' => 'active'],
        );

        return Course::query()->create([
            'category_id' => $category->id,
            'title' => $title,
            'slug' => $slug,
            'short_description' => 'Preparation.',
            'status' => 'active',
            'price' => 0,
            'price_on_request' => true,
        ]);
    }
}
