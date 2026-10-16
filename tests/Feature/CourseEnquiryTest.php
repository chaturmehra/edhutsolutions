<?php

namespace Tests\Feature;

use App\Mail\EnrollmentRequestMail;
use App\Models\Category;
use App\Models\ContactEnquiry;
use App\Models\Course;
use App\Models\Trainer;
use App\Services\Settings\SiteSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class CourseEnquiryTest extends TestCase
{
    use RefreshDatabase;

    public function test_course_page_shows_the_enrollment_form_below_the_trainers(): void
    {
        $pmp = $this->course('pmp-certification-training', 'Project Management Professional (PMP)');
        $this->course('pgmp-certification-training', 'Program Management Professional (PgMP)');
        $trainer = Trainer::query()->create(['name' => 'Asha Mentor', 'status' => 'active']);
        $trainer->courses()->sync([$pmp->id]);

        $this->get(route('courses.show', $pmp))
            ->assertOk()
            ->assertSeeInOrder(['Asha Mentor', 'Enroll in PMP®', 'Reserve your seat in the next cohort.', 'Speak with a program advisor.'])
            ->assertSee('action="'.route('courses.enquiry', $pmp).'"', false)
            ->assertSee('<option value="PMP" selected>PMP</option>', false)
            ->assertSee('<option value="PgMP" >PgMP</option>', false)
            ->assertSeeInOrder(['Select range', '0-2 years', '3-5 years', '6-10 years', '11-15 years', '15+ years'])
            ->assertSee('placeholder="e.g. Senior Project Manager"', false)
            ->assertDontSee('24–48');
    }

    public function test_enrollment_request_is_saved_and_mailed_to_the_site_email(): void
    {
        Mail::fake();
        $pmp = $this->course('pmp-certification-training', 'Project Management Professional (PMP)');

        $this->post(route('courses.enquiry', $pmp), [
            'name' => 'Ravi Kumar',
            'email' => 'ravi@example.com',
            'phone' => '919000000000',
            'certification' => 'PMP',
            'experience' => '6-10 years',
            'role' => 'Senior Project Manager',
            'message' => 'Prefer weekend batches.',
        ])
            ->assertRedirect(route('courses.show', $pmp).'#enroll')
            ->assertSessionHas('toast', 'Thank you. Your enrollment request has been sent to the EdHut team.')
            ->assertSessionHas('toast_ok', true);

        $enquiry = ContactEnquiry::query()->sole();
        $this->assertSame('Enrollment request: PMP', $enquiry->subject);
        $this->assertSame('919000000000', $enquiry->phone);
        $this->assertStringContainsString('Experience: 6-10 years', $enquiry->message);
        $this->assertStringContainsString('Current role: Senior Project Manager', $enquiry->message);
        $this->assertStringContainsString('Prefer weekend batches.', $enquiry->message);

        $siteEmail = app(SiteSettings::class)->get('email');
        Mail::assertSent(EnrollmentRequestMail::class, function (EnrollmentRequestMail $mail) use ($siteEmail) {
            $body = $mail->render();

            return $mail->hasTo($siteEmail)
                && $mail->hasReplyTo('ravi@example.com')
                && $mail->hasSubject('EdHut enrollment request: PMP · Ravi Kumar')
                && str_contains($body, 'Experience: 6-10 years')
                && str_contains($body, 'Prefer weekend batches.');
        });
    }

    public function test_enrollment_request_rejects_missing_phone_and_unknown_options(): void
    {
        Mail::fake();
        $pmp = $this->course('pmp-certification-training', 'Project Management Professional (PMP)');

        $this->from(route('courses.show', $pmp))
            ->post(route('courses.enquiry', $pmp), [
                'name' => 'Ravi Kumar',
                'email' => 'ravi@example.com',
                'certification' => 'CAPM',
                'experience' => '30 years',
            ])
            ->assertSessionHasErrors(['phone', 'certification', 'experience']);

        $this->from(route('courses.show', $pmp))
            ->post(route('courses.enquiry', $pmp), [
                'name' => 'Ravi Kumar',
                'email' => 'ravi@example.com',
                'phone' => '+91 90000 00000',
                'certification' => 'PMP',
            ])
            ->assertSessionHasErrors(['phone']);

        $this->assertSame(0, ContactEnquiry::query()->count());
        Mail::assertNothingSent();
    }

    private function course(string $slug, string $title): Course
    {
        $category = Category::query()->firstOrCreate(
            ['slug' => 'project-management'],
            ['name' => 'Project Management', 'status' => 'active'],
        );

        return Course::query()->create([
            'category_id' => $category->id,
            'title' => $title,
            'slug' => $slug,
            'short_description' => 'Preparation.',
            'status' => 'active',
            'price' => 0,
            'price_on_request' => true,
        ]);
    }
}
