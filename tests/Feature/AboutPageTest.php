<?php

namespace Tests\Feature;

use App\Models\Page;
use Database\Seeders\SettingSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AboutPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_about_page_shows_stats_founder_principles_studio_and_cta_in_order(): void
    {
        $this->seed(SettingSeeder::class);
        Page::query()->create(['title' => 'About Us', 'slug' => 'about', 'content' => '<p>About.</p>', 'status' => 'active']);

        $this->get('/about-us')
            ->assertOk()
            ->assertSeeInOrder([
                'A PMI Authorized Training Partner for project, program and portfolio professionals.',
                'class="section about-stats band-tint"',
                'Students worldwide',
                'Meet the founder',
                'class="section about-principles band-plain"',
                'Our principles.',
                'PMI standards',
                'Support around the exam',
                'class="section about-studio band-tint"',
                'Based in Okhla, New Delhi.',
                'Why professionals choose EdHut',
                'class="section band-plain"',
                'Ready to prepare for a PMI credential?',
            ], false);
    }
}
