<?php

namespace Tests\Feature;

use App\Models\ContactEnquiry;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class FormToastTest extends TestCase
{
    use RefreshDatabase;

    public function test_phone_inputs_only_accept_digits(): void
    {
        Service::query()->create(['title' => 'Corporate Training', 'slug' => 'corporate-training', 'short_description' => 'Team training.', 'status' => 'active']);

        foreach ([route('contact'), route('register'), route('services.show', 'corporate-training')] as $url) {
            $this->get($url)
                ->assertOk()
                ->assertSee('name="phone" inputmode="numeric" pattern="[0-9]{7,15}" maxlength="15"', false)
                ->assertSee('data-digits', false);
        }
    }

    public function test_contact_form_reports_success_in_a_toast(): void
    {
        Mail::fake();

        $this->from(route('contact'))
            ->post(route('contact.store'), [
                'name' => 'Asha Rao',
                'email' => 'asha@example.com',
                'phone' => '9876543210',
                'message' => 'Tell me about PMP cohorts.',
            ])
            ->assertRedirect(route('contact'))
            ->assertSessionHas('toast', 'Thank you. Your enquiry has been received.')
            ->assertSessionHas('toast_ok', true)
            ->assertSessionMissing('status');

        $this->assertSame('9876543210', ContactEnquiry::query()->sole()->phone);

        $this->followingRedirects()
            ->from(route('contact'))
            ->post(route('contact.store'), [
                'name' => 'Asha Rao',
                'email' => 'asha@example.com',
                'message' => 'Second note.',
            ])
            ->assertSeeInOrder(['toast-stack', 'toast is-ok', 'Thank you. Your enquiry has been received.', '<main id="main">'], false)
            ->assertDontSee('class="alert"', false);
    }

    public function test_contact_form_errors_show_in_a_toast_not_on_the_page(): void
    {
        $this->followingRedirects()
            ->from(route('contact'))
            ->post(route('contact.store'), [
                'name' => 'Asha Rao',
                'email' => 'asha@example.com',
                'phone' => '+91 98765',
                'message' => 'Hello.',
            ])
            ->assertSeeInOrder(['toast is-bad', 'The phone field must be between 7 and 15 digits.', '<main id="main">'], false)
            ->assertSee('aria-invalid="true"', false)
            ->assertDontSee('alert-error', false);

        $this->assertSame(0, ContactEnquiry::query()->count());
    }

    public function test_register_form_uses_toasts_for_errors_and_success(): void
    {
        User::factory()->create(['email' => 'taken@example.com']);

        $this->followingRedirects()
            ->from(route('register'))
            ->post(route('register'), [
                'name' => 'New Learner',
                'email' => 'taken@example.com',
                'phone' => '98765abc',
                'password' => 'Password#2026',
                'password_confirmation' => 'Password#2026',
            ])
            ->assertSeeInOrder(['toast is-bad', 'The email has already been taken.', 'The phone field must be between 7 and 15 digits.', '<main id="main">'], false)
            ->assertDontSee('alert-error', false);

        $this->post(route('register'), [
            'name' => 'New Learner',
            'email' => 'new@example.com',
            'phone' => '9876543210',
            'password' => 'Password#2026',
            'password_confirmation' => 'Password#2026',
        ])
            ->assertRedirect(route('verification.notice'))
            ->assertSessionHas('toast', 'Your account has been created. Please verify your email address.');

        $this->assertSame('9876543210', User::query()->where('email', 'new@example.com')->sole()->phone);
    }
}
