<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Course;
use App\Models\Order;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PackageOwnershipTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_the_bought_course_package_shows_continue_learning(): void
    {
        $course = $this->course();
        $silver = $course->packages()->create(['name' => 'Silver', 'price' => 14999, 'status' => 'active', 'sort_order' => 1]);
        $gold = $course->packages()->create(['name' => 'Gold', 'price' => 19999, 'status' => 'active', 'sort_order' => 2]);
        $course->packages()->create(['name' => 'Platinum', 'price' => 24999, 'status' => 'active', 'sort_order' => 3]);
        $course->schedules()->create(['starts_on' => now()->addWeek(), 'status' => 'active']);
        $student = User::factory()->create();
        $admin = User::factory()->superAdmin()->create();

        $this->actingAs($student)->post(route('cart.store', $course), ['package' => $silver->id]);
        $this->checkout($student);
        $this->actingAs($admin)->post(route('admin.orders.approve', Order::query()->sole()))->assertRedirect();

        $this->actingAs($student)->get(route('courses.show', $course))
            ->assertOk()
            ->assertSee('Continue learning')
            ->assertDontSee('Enroll · Silver')
            ->assertSee('Enroll · Gold')
            ->assertSee('Enroll · Platinum')
            ->assertSee('Purchased');

        $this->actingAs($student)->post(route('cart.store', $course), ['package' => $silver->id])
            ->assertSessionHas('toast', 'You already bought the PMP Silver package.')
            ->assertSessionHas('toast_ok', false);
        $this->assertDatabaseCount('cart_items', 0);

        $this->actingAs($student)->post(route('cart.store', $course), ['package' => $gold->id])->assertSessionHas('toast_ok', true);
        $this->checkout($student);
        $upgrade = Order::query()->where('status', 'pending')->sole();

        $this->actingAs($student)->get(route('courses.show', $course))
            ->assertSee('Continue learning')
            ->assertSee(route('account.orders.show', $upgrade))
            ->assertDontSee('Enroll · Gold')
            ->assertSee('Enroll · Platinum')
            ->assertSee('Order pending');
    }

    public function test_a_whole_course_enrollment_owns_every_package(): void
    {
        $course = $this->course();
        $course->packages()->create(['name' => 'Silver', 'price' => 14999, 'status' => 'active']);
        $course->packages()->create(['name' => 'Gold', 'price' => 19999, 'status' => 'active']);
        $course->schedules()->create(['starts_on' => now()->addWeek(), 'status' => 'active']);
        $student = User::factory()->create();
        $student->enrollments()->create(['course_id' => $course->id, 'status' => 'active', 'enrolled_at' => now()]);

        $this->actingAs($student)->get(route('courses.show', $course))
            ->assertSee('Continue learning')
            ->assertDontSee('Enroll · Silver')
            ->assertDontSee('Enroll · Gold')
            ->assertDontSee('Select training date');
    }

    public function test_only_the_bought_service_plan_shows_continue_learning(): void
    {
        $service = Service::query()->create([
            'title' => 'Application Writing Support',
            'slug' => 'application-writing-support',
            'short_description' => 'Help preparing a PMI application.',
            'status' => 'active',
        ]);
        $pmp = $service->plans()->create(['label' => 'PMP', 'price' => 2500, 'currency' => 'INR', 'status' => 'active', 'sort_order' => 1]);
        $pgmp = $service->plans()->create(['label' => 'PgMP', 'price' => 6000, 'currency' => 'INR', 'status' => 'active', 'sort_order' => 2]);
        $student = User::factory()->create();
        $admin = User::factory()->superAdmin()->create();

        $this->actingAs($student)->post(route('cart.plans.store', $pmp->id));
        $this->checkout($student);
        $order = Order::query()->sole();

        $this->actingAs($student)->get(route('services.show', $service))
            ->assertSee(route('account.orders.show', $order))
            ->assertSee(route('cart.plans.store', $pgmp->id))
            ->assertDontSee(route('cart.plans.store', $pmp->id));
        $this->actingAs($student)->post(route('cart.plans.store', $pmp->id))->assertSessionHas('toast_ok', false);

        $this->actingAs($admin)->post(route('admin.orders.approve', $order))->assertRedirect();

        $this->actingAs($student)->get(route('services.show', $service))
            ->assertSee(route('learn.services.show', $service))
            ->assertSee('Continue learning')
            ->assertSee(route('cart.plans.store', $pgmp->id))
            ->assertDontSee(route('cart.plans.store', $pmp->id));
        $this->actingAs($student)->get(route('home'))
            ->assertSee(route('learn.services.show', $service))
            ->assertSee(route('cart.plans.store', $pgmp->id))
            ->assertDontSee(route('cart.plans.store', $pmp->id));

        $this->actingAs($student)->post(route('cart.plans.store', $pmp->id))
            ->assertSessionHas('toast', 'You already bought Application Writing Support (PMP).');
        $this->actingAs($student)->post(route('cart.plans.store', $pgmp->id))->assertSessionHas('toast_ok', true);
    }

    private function checkout(User $student): void
    {
        $this->actingAs($student)->post(route('checkout.store'), [
            'name' => 'Learner',
            'phone' => '9876543210',
            'country' => 'IN',
        ])->assertRedirect()->assertSessionHasNoErrors();
    }

    private function course(): Course
    {
        $category = Category::query()->create(['name' => 'Project Management', 'slug' => 'project-management', 'status' => 'active']);

        return Course::query()->create([
            'category_id' => $category->id,
            'title' => 'Project Management Professional (PMP)',
            'slug' => 'pmp-certification-training',
            'short_description' => 'Preparation.',
            'status' => 'active',
            'price' => 0,
            'price_on_request' => true,
        ]);
    }
}
