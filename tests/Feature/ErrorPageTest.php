<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ErrorPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_unknown_url_shows_the_branded_404_page(): void
    {
        $this->get('/this-page-does-not-exist')
            ->assertNotFound()
            ->assertSee('<meta name="robots" content="noindex">', false)
            ->assertSee('This page is off the project plan.')
            ->assertSee('href="'.route('home').'"', false)
            ->assertSeeInOrder(['Popular destinations', 'Certifications', 'Services', 'Corporate Training', 'Blogs'])
            ->assertSee('href="'.route('services.show', 'corporate-training').'"', false);
    }

    public function test_missing_course_uses_the_same_404_page(): void
    {
        $this->get(route('courses.show', 'no-such-course'))
            ->assertNotFound()
            ->assertSee('This page is off the project plan.');
    }
}
