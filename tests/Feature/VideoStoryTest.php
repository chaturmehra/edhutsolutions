<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\VideoStory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VideoStoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_hides_video_stories_until_one_is_published(): void
    {
        $this->get('/')->assertOk()->assertDontSee('home-stories', false);

        VideoStory::query()->create([
            'name' => 'Draft Story',
            'video_url' => 'https://www.youtube.com/watch?v=abcdefghijk',
            'status' => 'inactive',
        ]);

        $this->get('/')->assertOk()->assertDontSee('Draft Story');
    }

    public function test_published_youtube_story_shows_with_a_player_and_thumbnail(): void
    {
        VideoStory::query()->create([
            'name' => 'Asha Learner',
            'designation' => 'Program Manager',
            'quote' => 'The mentoring helped.',
            'video_url' => 'https://youtu.be/abcdefghijk',
            'duration' => '2:07',
            'status' => 'active',
        ]);

        $this->get('/')
            ->assertOk()
            ->assertSee('home-stories', false)
            ->assertSee('Asha Learner')
            ->assertSee('2:07')
            ->assertSee('data-story-youtube="abcdefghijk"', false)
            ->assertSee('https://i.ytimg.com/vi/abcdefghijk/hqdefault.jpg', false);
    }

    public function test_admin_can_add_a_video_story(): void
    {
        $admin = User::factory()->superAdmin()->create();

        $this->actingAs($admin)->get('/admin/video-stories')->assertOk();

        $this->actingAs($admin)->post('/admin/video-stories', [
            'name' => 'Asha Learner',
            'video_url' => 'not a link',
            'status' => 'active',
        ])->assertSessionHasErrors('video_url');

        $this->actingAs($admin)->post('/admin/video-stories', [
            'name' => 'Asha Learner',
            'video_url' => 'https://www.youtube.com/watch?v=abcdefghijk',
            'status' => 'active',
        ])->assertRedirect('/admin/video-stories');

        $this->assertDatabaseHas('video_stories', ['name' => 'Asha Learner', 'sort_order' => 0]);
    }
}
