<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Course;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomeCohortTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_lists_running_and_upcoming_active_cohorts(): void
    {
        $this->get('/')->assertOk()->assertDontSee('home-cohorts', false);

        $course = $this->course();
        $course->schedules()->create([
            'starts_on' => today()->subDays(10),
            'ends_on' => today()->subDay(),
            'hours_label' => 'Past batch hours',
            'status' => 'active',
        ]);
        $course->schedules()->create([
            'starts_on' => today()->subDays(3),
            'ends_on' => today()->addDays(4),
            'hours_label' => 'Running batch hours',
            'status' => 'active',
        ]);
        $course->schedules()->create([
            'starts_on' => today()->addDays(20),
            'hours_label' => 'Hidden batch hours',
            'status' => 'inactive',
        ]);
        $course->schedules()->create([
            'starts_on' => today()->addDays(10),
            'ends_on' => today()->addDays(18),
            'time_label' => '9:30 AM to 6:30 PM (IST)',
            'hours_label' => '36 hrs - 4 sessions',
            'status' => 'active',
        ]);

        $this->get('/')
            ->assertOk()
            ->assertSee('home-cohorts', false)
            ->assertSee('PMP cohort, '.today()->addDays(10)->format('F Y'))
            ->assertSee('36 hrs - 4 sessions')
            ->assertSee('Trainer: PMI Authorized Trainer')
            ->assertSee('Request quotation')
            ->assertSeeInOrder(['Running batch hours', '36 hrs - 4 sessions'])
            ->assertSee('In progress')
            ->assertDontSee('Past batch hours')
            ->assertDontSee('Hidden batch hours')
            ->assertDontSee('data-home-modal-open="cohort-modal-', false);
    }

    public function test_cohort_card_shows_mode_and_package_price_and_reserves_from_the_popup(): void
    {
        $course = $this->course();
        $cohort = $course->schedules()->create([
            'starts_on' => today()->addDays(10),
            'ends_on' => today()->addDays(30),
            'delivery_mode' => 'Virtual (Weekend)',
            'status' => 'active',
        ]);
        $course->packages()->create(['name' => 'Silver', 'price' => 14999, 'compare_price' => 19999, 'currency' => 'INR', 'features' => "Live sessions\nStudy guide", 'sort_order' => 1, 'status' => 'active']);
        $gold = $course->packages()->create(['name' => 'Gold', 'price' => 19999, 'currency' => 'INR', 'includes_label' => 'Everything listed in Silver package, plus:', 'is_popular' => true, 'sort_order' => 2, 'status' => 'active']);
        $course->packages()->create(['name' => 'Retired', 'price' => 999, 'currency' => 'INR', 'status' => 'inactive']);

        $html = $this->get('/')
            ->assertOk()
            ->assertSeeInOrder(['Virtual (Weekend)', 'Trainer: PMI Authorized Trainer'])
            ->assertSeeInOrder(['From', 'INR 19,999', 'INR 14,999', '25% OFF'])
            ->assertSee('data-home-modal-open="cohort-modal-'.$cohort->id.'"', false)
            ->assertSee('Choose your package')
            ->assertSee('Everything listed in Silver package, plus:')
            ->assertSee('Study guide')
            ->assertDontSee('Retired')
            ->assertDontSee('Request quotation')
            ->getContent();

        $this->assertMatchesRegularExpression('/name="package" value="'.$gold->id.'"\s+checked/', $html);

        $this->post(route('cart.store', $course), [
            'package' => $gold->id,
            'schedule' => $cohort->id,
        ])->assertRedirect()->assertSessionHas('toast', 'PMP Gold package ('.$cohort->rangeLabel().') was added to your cart.');
    }

    public function test_a_cohort_stays_open_on_the_course_page_and_cart_until_its_end_date(): void
    {
        $course = $this->course();
        $running = $course->schedules()->create([
            'starts_on' => today()->subDays(2),
            'ends_on' => today()->addDays(5),
            'status' => 'active',
        ]);
        $ended = $course->schedules()->create([
            'starts_on' => today()->subDays(9),
            'ends_on' => today()->subDay(),
            'status' => 'active',
        ]);

        $this->get(route('courses.show', $course))
            ->assertOk()
            ->assertSee($running->rangeLabel())
            ->assertSee('In progress')
            ->assertDontSee($ended->rangeLabel());

        $this->post(route('cart.store', $course), ['schedule' => $ended->id])->assertNotFound();
        $this->post(route('cart.store', $course), ['schedule' => $running->id])
            ->assertRedirect()
            ->assertSessionHas('toast', 'PMP ('.$running->rangeLabel().') was added to your cart.');
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
