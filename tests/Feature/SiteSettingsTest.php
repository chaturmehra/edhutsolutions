<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use App\Services\Settings\SiteSettings;
use Database\Seeders\SettingSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SiteSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_footer_links_about_us_and_every_filled_social_profile(): void
    {
        $this->seed(SettingSeeder::class);

        $this->get(route('contact'))
            ->assertOk()
            ->assertSee('href="'.route('pages.show', 'about').'">About Us</a>', false)
            ->assertSee('href="https://x.com/info_edhut" aria-label="X (Twitter)"', false)
            ->assertSee('aria-label="LinkedIn"', false);

        Setting::query()->where('key', 'x')->update(['value' => '']);
        app(SiteSettings::class)->forget();

        $this->get(route('contact'))->assertDontSee('aria-label="X (Twitter)"', false);
    }

    public function test_admin_settings_are_grouped_into_cards(): void
    {
        $this->seed(SettingSeeder::class);
        $admin = User::factory()->superAdmin()->create();

        $this->actingAs($admin)->get(route('admin.settings.edit'))
            ->assertOk()
            ->assertSeeInOrder(['Settings', 'Site name', 'Contact details', 'Email', 'Social media', 'Facebook', 'X (Twitter)', 'Stats and reviews', 'Notice popup'])
            ->assertSee('name="settings[x]" value="https://x.com/info_edhut"', false);

        $this->actingAs($admin)->put(route('admin.settings.update'), ['settings' => ['x' => 'https://x.com/edhut_new']])
            ->assertRedirect();
        $this->assertSame('https://x.com/edhut_new', Setting::query()->where('key', 'x')->value('value'));
    }
}
