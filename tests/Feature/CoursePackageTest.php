<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Course;
use App\Models\Trainer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CoursePackageTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_adds_packages_that_show_on_the_course_page(): void
    {
        $course = $this->course();
        $admin = User::factory()->superAdmin()->create();

        $this->actingAs($admin)->post(route('admin.courses.packages.store', $course->id), [
            'name' => 'Gold',
            'price' => 30000,
            'compare_price' => 50000,
            'includes_label' => 'Everything listed in Silver package, plus:',
            'features' => "Live sessions\n\nMock exams",
            'is_popular' => 1,
            'sort_order' => 2,
            'status' => 'active',
        ])->assertSessionHasNoErrors()->assertRedirect(route('admin.courses.materials', $course->id));

        $this->actingAs($admin)->post(route('admin.courses.packages.store', $course->id), [
            'name' => 'Hidden',
            'price' => 100,
            'status' => 'inactive',
        ])->assertSessionHasNoErrors();

        $this->actingAs($admin)->post(route('admin.courses.packages.store', $course->id), [
            'name' => 'Broken',
            'price' => 500,
            'compare_price' => 400,
            'status' => 'active',
        ])->assertSessionHasErrors('compare_price');

        $this->actingAs($admin)->get(route('admin.courses.materials', $course->id))
            ->assertOk()
            ->assertSee('Gold')
            ->assertSee('40% off');

        auth()->logout();
        $this->get(route('courses.show', $course))
            ->assertOk()
            ->assertSee('Choose the package that matches your goal.')
            ->assertSee('INR 30,000')
            ->assertSee('INR 50,000')
            ->assertSee('40% OFF')
            ->assertSee('Most Popular')
            ->assertSee('Everything listed in Silver package, plus:')
            ->assertSee('Mock exams')
            ->assertDontSee('Enroll · Gold')
            ->assertDontSee('Select training date');

        $course->schedules()->create(['starts_on' => now()->addWeek(), 'status' => 'active']);

        $this->get(route('courses.show', $course))
            ->assertSee('Enroll · Gold')
            ->assertSee('Select training date')
            ->assertDontSee('Enroll · Hidden');
    }

    public function test_course_page_hides_the_section_without_active_packages(): void
    {
        $course = $this->course();

        $this->get(route('courses.show', $course))
            ->assertOk()
            ->assertDontSee('Choose the package that matches your goal.');
    }

    public function test_enrolling_in_a_package_carries_its_price_into_the_order(): void
    {
        $course = $this->course();
        $silver = $course->packages()->create(['name' => 'Silver', 'price' => 20000, 'status' => 'active', 'sort_order' => 1]);
        $gold = $course->packages()->create(['name' => 'Gold', 'price' => 30000, 'status' => 'active', 'sort_order' => 2]);
        $other = $this->course('pgmp-certification-training');
        $foreign = $other->packages()->create(['name' => 'Gold', 'price' => 1, 'status' => 'active']);
        $student = User::factory()->create();

        $this->actingAs($student)->post(route('cart.store', $course))
            ->assertRedirect(route('courses.show', $course).'#packages');
        $this->assertDatabaseCount('cart_items', 0);

        $this->actingAs($student)->post(route('cart.store', $course), ['package' => $foreign->id])->assertNotFound();

        $this->actingAs($student)->post(route('cart.store', $course), ['package' => $silver->id])->assertSessionHas('toast_ok', true);
        $this->actingAs($student)->post(route('cart.store', $course), ['package' => $gold->id])->assertSessionHas('toast_ok', true);
        $this->assertDatabaseCount('cart_items', 1);
        $this->assertDatabaseHas('cart_items', ['course_id' => $course->id, 'course_package_id' => $gold->id, 'price' => 30000]);

        $this->actingAs($student)->get(route('courses.show', $course))->assertSee('In your cart');
        $this->actingAs($student)->get('/cart')->assertSee($course->title.' – Gold package')->assertSee('₹30,000');

        $this->actingAs($student)->post('/checkout', [
            'name' => 'Learner',
            'phone' => '9876543210',
            'country' => 'IN',
        ])->assertRedirect();

        $this->assertDatabaseHas('orders', ['user_id' => $student->id, 'total' => 30000]);
        $this->assertDatabaseHas('order_items', [
            'course_id' => $course->id,
            'course_package_id' => $gold->id,
            'title' => $course->title.' – Gold package',
            'price' => 30000,
        ]);
    }

    public function test_admin_cohort_shows_in_the_popup_and_follows_the_order(): void
    {
        $this->travelTo('2026-10-01 10:00:00');
        $course = $this->course();
        $gold = $course->packages()->create(['name' => 'Gold', 'price' => 19999, 'status' => 'active']);
        $admin = User::factory()->superAdmin()->create();
        $lead = Trainer::query()->create(['name' => 'Lead Trainer', 'status' => 'active']);
        $deleted = Trainer::query()->create(['name' => 'Gone Trainer', 'status' => 'active']);
        $deleted->delete();

        $this->actingAs($admin)->get(route('admin.courses.cohorts.create', $course->id))
            ->assertOk()
            ->assertSee('<option value="">PMI Authorized Trainer (default)</option>', false)
            ->assertSee('<option value="'.$lead->id.'" >Lead Trainer</option>', false)
            ->assertDontSee('Gone Trainer');

        $this->actingAs($admin)->post(route('admin.courses.cohorts.store', $course->id), [
            'starts_on' => '2026-10-12',
            'trainer_id' => $deleted->id,
            'status' => 'active',
        ])->assertSessionHasErrors('trainer_id');

        $this->actingAs($admin)->post(route('admin.courses.cohorts.store', $course->id), [
            'starts_on' => '2026-10-10',
            'ends_on' => '2026-10-31',
            'time_label' => '8:00 AM - 1:00 PM (GMT+5:30)',
            'hours_label' => '35 hours / 7 sessions',
            'delivery_mode' => 'Virtual (Weekend)',
            'trainer_id' => $lead->id,
            'session_dates' => ['2026-10-11', '2026-10-10', '', '2026-10-11'],
            'status' => 'active',
        ])->assertSessionHasNoErrors()->assertRedirect(route('admin.courses.materials', $course->id));

        $this->actingAs($admin)->post(route('admin.courses.cohorts.store', $course->id), [
            'starts_on' => '2026-10-20',
            'ends_on' => '2026-10-05',
            'status' => 'active',
        ])->assertSessionHasErrors('ends_on');

        $cohort = $course->schedules()->first();
        $this->assertSame(['2026-10-10', '2026-10-11'], $cohort->session_dates);
        $past = $course->schedules()->create(['starts_on' => '2026-09-01', 'ends_on' => '2026-09-20', 'status' => 'active']);

        $this->actingAs($admin)->get(route('admin.courses.materials', $course->id))
            ->assertSee('10 Oct - 31 Oct 2026')
            ->assertSee('Virtual (Weekend)');
        auth()->logout();

        $this->get(route('courses.show', $course))
            ->assertSee('Select training date')
            ->assertSee('data-cohort-open', false)
            ->assertSee('10 Oct - 31 Oct 2026')
            ->assertSee('Trainer: Lead Trainer')
            ->assertSee('<strong>11 Oct</strong><span>Sun</span>', false)
            ->assertDontSee('01 Sep - 20 Sep 2026');

        $lead->update(['status' => 'inactive']);
        $this->get(route('courses.show', $course))
            ->assertDontSee('Trainer: Lead Trainer')
            ->assertSee('Trainer: PMI Authorized Trainer');
        $lead->update(['status' => 'active']);

        $student = User::factory()->create();
        $this->actingAs($student)->post(route('cart.store', $course), ['package' => $gold->id, 'schedule' => $past->id])->assertNotFound();
        $this->actingAs($student)->post(route('cart.store', $course), ['package' => $gold->id, 'schedule' => $cohort->id, 'buy_now' => 1])
            ->assertRedirect(route('checkout.create'));
        $this->assertDatabaseHas('cart_items', ['course_package_id' => $gold->id, 'course_schedule_id' => $cohort->id]);

        $this->actingAs($student)->post('/checkout', [
            'name' => 'Learner',
            'phone' => '9876543210',
            'country' => 'IN',
        ])->assertRedirect();
        $this->assertDatabaseHas('order_items', [
            'course_schedule_id' => $cohort->id,
            'title' => $course->title.' – Gold package · 10 Oct - 31 Oct 2026',
        ]);
    }

    public function test_upcoming_cohorts_section_lists_sessions_and_the_entry_price(): void
    {
        $this->travelTo('2026-10-01 10:00:00');
        $course = $this->course();
        $silver = $course->packages()->create(['name' => 'Silver', 'price' => 14999, 'compare_price' => 19999, 'status' => 'active']);
        $course->packages()->create(['name' => 'Gold', 'price' => 19999, 'status' => 'active']);
        $weekend = $course->schedules()->create([
            'starts_on' => '2026-10-10',
            'ends_on' => '2026-10-18',
            'session_dates' => ['2026-10-10', '2026-10-11', '2026-10-17', '2026-10-18'],
            'status' => 'active',
        ]);
        $course->schedules()->create(['starts_on' => '2026-11-02', 'ends_on' => '2026-11-04', 'session_dates' => ['2026-11-02', '2026-11-03', '2026-11-04'], 'status' => 'active']);
        $course->schedules()->create(['starts_on' => '2026-12-01', 'status' => 'inactive']);

        $this->assertSame('2 Weekends', $weekend->scheduleSummary());

        $this->get(route('courses.show', $course))
            ->assertSee('Upcoming PMP cohorts.')
            ->assertSeeInOrder(['10 Oct - 18 Oct 2026', 'INR 19,999', 'INR 14,999', 'Schedule (2 Weekends)', 'Session 4', '18 Oct 2026 (Sun)'])
            ->assertSee('Schedule (3 Sessions)')
            ->assertSee('data-schedule-id="'.$weekend->id.'"', false)
            ->assertSee('data-package-id="'.$silver->id.'"', false)
            ->assertDontSee('01 Dec 2026');

        $other = $this->course('pgmp-certification-training');
        $this->get(route('courses.show', $other))
            ->assertSee('Upcoming PgMP cohorts.')
            ->assertSee('New cohort dates are being announced soon.')
            ->assertDontSee('cohort-row', false);
    }

    private function course(string $slug = 'pmp-certification-training'): Course
    {
        $category = Category::query()->firstOrCreate(
            ['slug' => 'project-management'],
            ['name' => 'Project Management', 'status' => 'active'],
        );

        return Course::query()->create([
            'category_id' => $category->id,
            'title' => $slug === 'pmp-certification-training' ? 'Project Management Professional (PMP)' : 'Program Management Professional (PgMP)',
            'slug' => $slug,
            'short_description' => 'Preparation.',
            'status' => 'active',
            'price' => 0,
            'price_on_request' => true,
        ]);
    }
}
