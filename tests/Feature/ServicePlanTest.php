<?php

namespace Tests\Feature;

use App\Models\OrderItem;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServicePlanTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_prices_show_on_the_homepage_card_and_popup(): void
    {
        $service = $this->service();
        $admin = User::factory()->superAdmin()->create();

        $this->actingAs($admin)->post(route('admin.services.plans.store', $service->id), [
            'label' => 'PgMP',
            'price' => 6000,
            'compare_price' => 8000,
            'sort_order' => 2,
            'status' => 'active',
        ])->assertSessionHasNoErrors()->assertRedirect(route('admin.services.plans.index', $service->id));

        $this->actingAs($admin)->post(route('admin.services.plans.store', $service->id), [
            'label' => 'PMP',
            'price' => 2500,
            'sort_order' => 1,
            'status' => 'active',
        ])->assertSessionHasNoErrors();

        $this->actingAs($admin)->post(route('admin.services.plans.store', $service->id), [
            'label' => 'Hidden',
            'price' => 100,
            'status' => 'inactive',
        ])->assertSessionHasNoErrors();

        $this->actingAs($admin)->post(route('admin.services.plans.store', $service->id), [
            'label' => 'Broken',
            'price' => 500,
            'compare_price' => 400,
            'status' => 'active',
        ])->assertSessionHasErrors('compare_price');

        $this->actingAs($admin)->get(route('admin.services.plans.index', $service->id))
            ->assertOk()
            ->assertSee('PgMP')
            ->assertSee('INR 8,000');
        $this->actingAs($admin)->get(route('admin.modules.index', 'services'))
            ->assertOk()
            ->assertSee(route('admin.services.plans.index', $service->id));

        auth()->logout();
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Everything you need on your PMI certification journey.')
            ->assertSee('Starting at')
            ->assertSee('INR 2,500')
            ->assertSee('data-home-modal-open="svc-modal-'.$service->id.'"', false)
            ->assertSee('Select your certification track')
            ->assertSee('INR 8,000')
            ->assertSee(route('cart.plans.store', $service->plans()->where('label', 'PgMP')->value('id')))
            ->assertDontSee('Hidden');
    }

    public function test_service_without_prices_links_to_its_page(): void
    {
        $service = $this->service();

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('On request')
            ->assertSee(route('services.show', $service))
            ->assertDontSee('svc-modal-', false);
    }

    public function test_buy_now_puts_the_plan_in_checkout_and_the_order(): void
    {
        $service = $this->service();
        $plan = $service->plans()->create(['label' => 'PMP', 'price' => 2500, 'currency' => 'INR', 'status' => 'active']);
        $hidden = $service->plans()->create(['label' => 'Old', 'price' => 10, 'currency' => 'INR', 'status' => 'inactive']);
        $student = User::factory()->create();

        $this->post(route('cart.plans.store', $hidden->id), ['buy_now' => 1])->assertNotFound();

        $this->post(route('cart.plans.store', $plan->id))->assertRedirect();
        $this->get(route('cart.index'))
            ->assertOk()
            ->assertSee('Application Writing Support – PMP')
            ->assertSee('INR 2,500');

        $this->actingAs($student)
            ->post(route('cart.plans.store', $plan->id), ['buy_now' => 1])
            ->assertRedirect(route('checkout.create'));

        $this->actingAs($student)->get(route('checkout.create'))
            ->assertOk()
            ->assertSee('Application Writing Support – PMP');

        $this->actingAs($student)->post(route('checkout.store'), [
            'name' => 'Asha Rao',
            'phone' => '9876543210',
            'country' => 'IN',
        ])->assertRedirect()->assertSessionHas('toast', 'Your order is placed. The EdHut team will confirm it after an administrator approves it.');

        $item = OrderItem::query()->sole();
        $this->assertSame($plan->id, $item->service_plan_id);
        $this->assertNull($item->course_id);
        $this->assertSame('Application Writing Support – PMP', $item->title);
        $this->assertEquals(2500, (float) $item->order->total);

        $this->actingAs($student)->get(route('account.orders.show', $item->order))->assertOk()->assertSee('Application Writing Support – PMP');
        $this->actingAs($student)->get(route('account.courses.index'))->assertOk();
        $this->actingAs($student)->get(route('account.dashboard'))->assertOk();
        $this->actingAs(User::factory()->superAdmin()->create())->get(route('admin.orders.show', $item->order))
            ->assertOk()
            ->assertSee('Application Writing Support – PMP');
    }

    private function service(): Service
    {
        return Service::query()->create([
            'title' => 'Application Writing Support',
            'slug' => 'application-writing-support',
            'short_description' => 'Help preparing a PMI application.',
            'sort_order' => 1,
            'status' => 'active',
        ]);
    }
}
