<?php

namespace Tests\Feature;

use App\Models\Country;
use App\Models\Order;
use App\Models\Service;
use App\Models\ServicePlan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class VisitorPricingTest extends TestCase
{
    use RefreshDatabase;

    public function test_countries_are_seeded_for_the_checkout_list(): void
    {
        $this->assertDatabaseHas('countries', ['code' => 'IN', 'name' => 'India']);
        $this->assertDatabaseHas('countries', ['code' => 'US', 'name' => 'United States']);
        $this->assertGreaterThan(240, Country::query()->count());
    }

    public function test_visitors_in_india_pay_in_inr(): void
    {
        $plan = $this->plan();
        $student = User::factory()->create();

        $this->get(route('home'))->assertSee('INR 2,500')->assertDontSee('USD 31.25');

        $this->actingAs($student)->post(route('cart.plans.store', $plan->id));
        $this->actingAs($student)->get(route('checkout.create'))
            ->assertOk()
            ->assertSee('<option value="IN" selected>India</option>', false)
            ->assertDontSee('name="state"', false);

        $this->actingAs($student)->post(route('checkout.store'), ['name' => 'Asha Rao', 'phone' => '9876543210', 'country' => 'ZZ'])
            ->assertSessionHasErrors(['country' => 'Select your country from the list.']);
        $this->actingAs($student)->post(route('checkout.store'), ['name' => 'Asha Rao', 'phone' => '9876543210'])
            ->assertSessionHasErrors(['country' => 'Select your country.']);

        $this->actingAs($student)->post(route('checkout.store'), ['name' => 'Asha Rao', 'phone' => '9876543210', 'country' => 'IN'])
            ->assertSessionHasNoErrors();
        $this->assertDatabaseHas('orders', ['user_id' => $student->id, 'currency' => 'INR', 'total' => 2500, 'country_code' => 'IN']);
    }

    public function test_visitors_outside_india_see_usd_with_the_markup(): void
    {
        $plan = $this->plan();
        $student = User::factory()->create();
        $foreign = ['CF-IPCountry' => 'US'];

        // 2,500 x 0.0105 + 5 = 31.25; 4,000 x 0.0105 + 5 = 47.00
        $this->withHeaders($foreign)->get(route('home'))
            ->assertSee('USD 31.25')
            ->assertSee('USD 47.00')
            ->assertSee('Prices in USD')
            ->assertDontSee('INR 2,500');
        $this->withHeaders($foreign)->get(route('services.show', $plan->service))
            ->assertSee('<small>USD</small> 31.25', false)
            ->assertSee('34% OFF');

        $this->actingAs($student)->withHeaders($foreign)->post(route('cart.plans.store', $plan->id));
        $this->actingAs($student)->withHeaders($foreign)->get(route('checkout.create'))
            ->assertSee('<option value="US" selected>United States</option>', false)
            ->assertSee('USD 31.25');

        $this->actingAs($student)->withHeaders($foreign)->post(route('checkout.store'), [
            'name' => 'Sam Lee',
            'phone' => '2025550143',
            'country' => 'GB',
        ])->assertSessionHasNoErrors();

        $order = Order::query()->sole();
        $this->assertSame('USD', $order->currency);
        $this->assertSame('GB', $order->country_code);
        $this->assertEquals(31.25, (float) $order->total);
        $this->assertEquals(31.25, (float) $order->items()->sole()->price);

        $this->actingAs($student)->get(route('account.orders.show', $order))->assertSee('USD 31.25');
        $this->actingAs(User::factory()->superAdmin()->create())->withHeaders($foreign)->get(route('admin.orders.show', $order))
            ->assertSee('USD 31.25')
            ->assertSee('United Kingdom');
        $this->withHeaders($foreign)->get(route('admin.services.plans.index', $plan->service_id))->assertSee('INR 2,500');
    }

    public function test_country_follows_the_visitor_ip_and_the_default_setting(): void
    {
        $this->plan();
        config(['edhut.geoip.lookup_url' => 'https://geo.test/{ip}']);
        Http::fake([
            'geo.test/203.0.113.9' => Http::response(['country' => 'US']),
            'geo.test/198.51.100.7' => Http::response(['country' => 'IN']),
        ]);

        $this->get(route('home'))->assertSee('INR 2,500');
        config(['edhut.geoip.default_country' => 'US']);
        $this->get(route('home'))->assertSee('USD 31.25');

        $this->withServerVariables(['REMOTE_ADDR' => '198.51.100.7'])->get(route('home'))->assertSee('INR 2,500');
        $this->withServerVariables(['REMOTE_ADDR' => '203.0.113.9'])->get(route('home'))->assertSee('USD 31.25');
        $this->withServerVariables(['REMOTE_ADDR' => '198.51.100.7'])->get(route('home'))->assertSee('INR 2,500');
        Http::assertSentCount(2);
    }

    private function plan(): ServicePlan
    {
        $service = Service::query()->create([
            'title' => 'Application Writing Support',
            'slug' => 'application-writing-support',
            'short_description' => 'Help preparing a PMI application.',
            'status' => 'active',
        ]);

        return $service->plans()->create(['label' => 'PMP', 'price' => 2500, 'compare_price' => 4000, 'currency' => 'INR', 'status' => 'active']);
    }
}
