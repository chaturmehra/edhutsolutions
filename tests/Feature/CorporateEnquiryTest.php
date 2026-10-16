<?php

namespace Tests\Feature;

use App\Mail\CorporateEnquiryMail;
use App\Models\Category;
use App\Models\ContactEnquiry;
use App\Models\Course;
use App\Models\Service;
use App\Services\Settings\SiteSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class CorporateEnquiryTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Service::query()->create([
            'title' => 'Corporate Training',
            'slug' => 'corporate-training',
            'short_description' => 'Custom PMI-aligned training for teams.',
            'status' => 'active',
        ]);
        $category = Category::query()->create(['name' => 'Project Management', 'slug' => 'project-management', 'status' => 'active']);
        foreach (['pmp' => 'Project Management Professional (PMP)', 'pgmp' => 'Program Management Professional (PgMP)'] as $slug => $title) {
            Course::query()->create([
                'category_id' => $category->id,
                'title' => $title,
                'slug' => $slug.'-certification-training',
                'short_description' => $title,
                'status' => 'active',
                'price' => 0,
                'price_on_request' => true,
            ]);
        }
    }

    public function test_corporate_page_renders_the_enquiry_form_options(): void
    {
        $this->get(route('services.show', 'corporate-training'))
            ->assertOk()
            ->assertSee('Enterprise training tailored to your organization.')
            ->assertSee('Design a cohort with us.')
            ->assertSee('action="'.route('corporate.enquiry').'"', false)
            ->assertSeeInOrder(['Select range', '1–10', '11–25', '26–50', '51–100', '100+'])
            ->assertSeeInOrder(['name="interest"', '>PMP<', '>PgMP<', '>Other<'], false)
            ->assertSeeInOrder(['Live Virtual', 'In-person', 'Hybrid'])
            ->assertSee('placeholder="e.g. Head of PMO"', false);
    }

    public function test_enquiry_is_saved_and_mailed_to_the_site_email(): void
    {
        Mail::fake();

        $this->post(route('corporate.enquiry'), [
            'name' => 'Asha Rao',
            'email' => 'asha@example.com',
            'phone' => '9000000000',
            'company' => 'Acme Infra',
            'designation' => 'Head of PMO',
            'team_size' => '26–50',
            'interest' => 'PgMP',
            'delivery' => 'Hybrid',
            'message' => 'Two cohorts this quarter.',
        ])
            ->assertRedirect(route('services.show', 'corporate-training').'#plan-cohort')
            ->assertSessionHas('toast', 'Thank you. Your corporate enquiry has been sent to the EdHut team.')
            ->assertSessionHas('toast_ok', true);

        $enquiry = ContactEnquiry::query()->sole();
        $this->assertSame('Corporate cohort request', $enquiry->subject);
        $this->assertStringContainsString('Company: Acme Infra', $enquiry->message);
        $this->assertStringContainsString('Team size: 26–50', $enquiry->message);
        $this->assertStringContainsString('Two cohorts this quarter.', $enquiry->message);

        $siteEmail = app(SiteSettings::class)->get('email');
        Mail::assertSent(CorporateEnquiryMail::class, function (CorporateEnquiryMail $mail) use ($siteEmail) {
            return $mail->hasTo($siteEmail)
                && $mail->hasReplyTo('asha@example.com')
                && $mail->hasSubject('EdHut corporate enquiry: Acme Infra')
                && str_contains($mail->render(), 'Preferred delivery: Hybrid');
        });
    }

    public function test_enquiry_rejects_missing_company_and_unknown_options(): void
    {
        Mail::fake();

        $this->from(route('services.show', 'corporate-training'))
            ->post(route('corporate.enquiry'), [
                'name' => 'Asha Rao',
                'email' => 'asha@example.com',
                'phone' => '90000-abc',
                'team_size' => '500',
                'interest' => 'CAPM',
                'delivery' => 'Self-paced',
            ])
            ->assertSessionHasErrors(['phone', 'company', 'team_size', 'interest', 'delivery']);

        $this->assertSame(0, ContactEnquiry::query()->count());
        Mail::assertNothingSent();
    }
}
