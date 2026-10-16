<?php

namespace Tests\Feature;

use App\Models\ContactEnquiry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminEnquiryTest extends TestCase
{
    use RefreshDatabase;

    public function test_enquiry_list_has_a_view_button_and_a_detail_popup(): void
    {
        $admin = User::factory()->superAdmin()->create();
        $enquiry = $this->enquiry();

        $this->actingAs($admin)->get(route('admin.enquiries.index'))
            ->assertOk()
            ->assertSee('<meta name="csrf-token"', false)
            ->assertSee('data-enquiry-open="enquiry-'.$enquiry->id.'"', false)
            ->assertSee('data-read-url="'.route('admin.enquiries.read', $enquiry).'"', false)
            ->assertSeeInOrder([
                '<dialog class="enquiry-dialog" id="enquiry-'.$enquiry->id.'"',
                'Corporate cohort request',
                'Asha Rao',
                'mailto:asha@example.com',
                'tel:9876543210',
                'Company: Acme Infra',
                'Two cohorts this quarter.',
            ], false);
    }

    public function test_opening_an_enquiry_marks_it_as_read(): void
    {
        $admin = User::factory()->superAdmin()->create();
        $enquiry = $this->enquiry();

        $this->actingAs($admin)
            ->patchJson(route('admin.enquiries.read', $enquiry))
            ->assertOk()
            ->assertExactJson(['status' => 'read']);
        $this->assertSame('read', $enquiry->fresh()->status);

        $this->actingAs($admin)
            ->patchJson(route('admin.enquiries.read', $enquiry))
            ->assertOk();
        $this->assertSame('read', $enquiry->fresh()->status);
    }

    public function test_only_admins_can_mark_enquiries_read(): void
    {
        $enquiry = $this->enquiry();

        $this->actingAs(User::factory()->create())
            ->patchJson(route('admin.enquiries.read', $enquiry))
            ->assertForbidden();
        $this->assertSame('new', $enquiry->fresh()->status);
    }

    private function enquiry(): ContactEnquiry
    {
        return ContactEnquiry::query()->create([
            'name' => 'Asha Rao',
            'email' => 'asha@example.com',
            'phone' => '9876543210',
            'subject' => 'Corporate cohort request',
            'message' => "Company: Acme Infra\n\nTwo cohorts this quarter.",
            'status' => 'new',
        ]);
    }
}
