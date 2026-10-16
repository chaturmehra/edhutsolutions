<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Course;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_sent_to_login_from_admin(): void
    {
        $this->get('/admin')->assertRedirect('/admin/login');
        $this->get('/admin/login')->assertOk();
    }

    public function test_student_cannot_open_admin_or_seo(): void
    {
        $student = User::factory()->create();

        $this->actingAs($student)->get('/admin')->assertForbidden();
        $this->actingAs($student)->get('/admin/seo')->assertForbidden();
        $this->actingAs($student)->get('/account')->assertOk();
    }

    public function test_seo_manager_can_open_only_the_seo_module(): void
    {
        $seo = User::factory()->seoManager()->create();

        $this->actingAs($seo)->get('/admin/seo')->assertOk();
        $this->actingAs($seo)->get('/admin')->assertForbidden();
        $this->actingAs($seo)->get('/account')->assertForbidden();
    }

    public function test_super_admin_can_open_dashboard_and_seo(): void
    {
        $admin = User::factory()->superAdmin()->create();

        $this->actingAs($admin)->get('/admin')->assertOk();
        $this->actingAs($admin)->get('/admin/seo')->assertOk();
    }

    public function test_blocked_user_cannot_stay_signed_in(): void
    {
        $user = User::factory()->blocked()->create();

        $this->actingAs($user)->get('/account')->assertRedirect('/login');
        $this->assertGuest();
    }

    public function test_inactive_course_is_hidden_from_the_public_site(): void
    {
        $category = Category::query()->create([
            'name' => 'Project Management',
            'slug' => 'project-management',
            'status' => 'active',
        ]);

        Course::query()->create([
            'category_id' => $category->id,
            'title' => 'Hidden Draft',
            'slug' => 'hidden-draft',
            'short_description' => 'Should not be public.',
            'status' => 'inactive',
            'price_on_request' => true,
        ]);

        $this->get('/courses')->assertDontSee('Hidden Draft');
        $this->get('/courses/hidden-draft')->assertNotFound();
    }

    public function test_demo_google_login_signs_in_the_demo_student(): void
    {
        User::factory()->create([
            'email' => 'demo@edhut.test',
            'name' => 'Demo Learner',
        ]);

        $this->get('/auth/google')->assertRedirect('/account');
        $this->assertAuthenticated();
    }
}
