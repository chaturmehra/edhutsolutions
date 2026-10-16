<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Course;
use App\Models\CourseVideo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourseVideoPlayerTest extends TestCase
{
    use RefreshDatabase;

    public function test_links_from_known_providers_play_inside_the_course(): void
    {
        $player = fn (string $url) => (new CourseVideo(['source_type' => 'link', 'external_url' => $url]))->player();

        $this->assertSame('https://www.youtube-nocookie.com/embed/dQw4w9WgXcQ?rel=0&modestbranding=1&autoplay=1', $player('https://www.youtube.com/watch?v=dQw4w9WgXcQ&t=10')['src']);
        $this->assertSame('frame', $player('https://youtu.be/dQw4w9WgXcQ')['type']);
        $this->assertStringContainsString('/embed/dQw4w9WgXcQ', $player('https://youtube.com/shorts/dQw4w9WgXcQ')['src']);
        $this->assertSame('https://player.vimeo.com/video/76979871?autoplay=1', $player('https://vimeo.com/76979871')['src']);
        $this->assertSame('https://player.vimeo.com/video/76979871?autoplay=1&h=abc123', $player('https://vimeo.com/76979871/abc123')['src']);
        $this->assertSame('https://drive.google.com/file/d/1AbCdEfGhIjKlMnOp/preview', $player('https://drive.google.com/file/d/1AbCdEfGhIjKlMnOp/view?usp=sharing')['src']);
        $this->assertSame('https://www.loom.com/embed/0123456789abcdef0123456789abcdef?autoplay=1', $player('https://www.loom.com/share/0123456789abcdef0123456789abcdef')['src']);
        $this->assertSame(['type' => 'file', 'src' => 'https://cdn.example.com/class.mp4?sig=1', 'protected' => false], $player('https://cdn.example.com/class.mp4?sig=1'));
        $this->assertNull($player('https://www.edhutsolutions.com/pmp'));
        $this->assertNull($player('javascript:alert(1)'));
    }

    public function test_video_tab_lists_videos_with_a_popup_player(): void
    {
        $category = Category::query()->create(['name' => 'Project Management', 'slug' => 'project-management', 'status' => 'active']);
        $course = Course::query()->create([
            'category_id' => $category->id,
            'title' => 'Project Management Professional (PMP)',
            'slug' => 'pmp-certification-training',
            'status' => 'active',
            'price' => 0,
        ]);
        $student = User::factory()->create();
        $student->enrollments()->create(['course_id' => $course->id, 'status' => 'active', 'enrolled_at' => now()]);

        $upload = CourseVideo::query()->create(['course_id' => $course->id, 'title' => 'Class recording', 'source_type' => 'upload', 'file_path' => 'courses/videos/a.mp4', 'duration' => '42 min', 'sort_order' => 1, 'status' => 'active']);
        CourseVideo::query()->create(['course_id' => $course->id, 'title' => 'Orientation', 'source_type' => 'link', 'external_url' => 'https://youtu.be/dQw4w9WgXcQ', 'sort_order' => 2, 'status' => 'active']);
        CourseVideo::query()->create(['course_id' => $course->id, 'title' => 'Webinar page', 'source_type' => 'link', 'external_url' => 'https://www.edhutsolutions.com/webinar', 'sort_order' => 3, 'status' => 'active']);

        $this->actingAs($student)->get(route('learn.show', $course))
            ->assertOk()
            ->assertSeeInOrder(['Class recording', '42 min', 'View', 'Orientation', 'View', 'Webinar page', 'Open link'])
            ->assertSee('data-video-src="'.route('learn.video', $upload).'"', false)
            ->assertSee('data-video-protected', false)
            ->assertSee('data-video-src="https://www.youtube-nocookie.com/embed/dQw4w9WgXcQ?rel=0&amp;modestbranding=1&amp;autoplay=1"', false)
            ->assertSee('href="https://www.edhutsolutions.com/webinar" target="_blank"', false)
            ->assertSee('data-video-dialog', false)
            ->assertSee('learn-video.js', false);
    }
}
