<?php

namespace Tests\Feature;

use App\Models\Blog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BlogCategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_blog_tabs_filter_posts_by_category(): void
    {
        $this->blog('PMP guide', 'Project Management');
        $this->blog('PgMP guide', 'Program Management');

        $this->get('/blogs')
            ->assertOk()
            ->assertSee('PMP guide')
            ->assertSee('PgMP guide')
            ->assertSee('blog-tab is-on', false)
            ->assertDontSee('Portfolio Management <span>', false);

        $this->get('/blogs?category=Program+Management')
            ->assertOk()
            ->assertSee('PgMP guide')
            ->assertDontSee('PMP guide');
    }

    public function test_admin_blog_form_uses_a_category_select(): void
    {
        $admin = User::factory()->superAdmin()->create();
        $blog = $this->blog('PMP guide', 'Project Management');

        $this->actingAs($admin)->get("/admin/blogs/{$blog->id}/edit")
            ->assertOk()
            ->assertSee('<select', false)
            ->assertSee('Portfolio Management')
            ->assertSee('Risk Management');

        $this->actingAs($admin)->put("/admin/blogs/{$blog->id}", [
            'title' => 'PMP guide',
            'slug' => 'pmp-guide',
            'category' => 'Not a category',
            'status' => 'active',
        ])->assertSessionHasErrors('category');
    }

    private function blog(string $title, string $category): Blog
    {
        return Blog::query()->create([
            'title' => $title,
            'slug' => str($title)->slug(),
            'category' => $category,
            'excerpt' => 'Excerpt',
            'content' => '<p>Body</p>',
            'published_on' => now()->toDateString(),
            'status' => 'active',
        ]);
    }
}
