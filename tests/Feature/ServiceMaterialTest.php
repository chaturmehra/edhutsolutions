<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Course;
use App\Models\CoursePdf;
use App\Models\CourseVideo;
use App\Models\Order;
use App\Models\Service;
use App\Models\ServiceEnrollment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ServiceMaterialTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_uploads_service_files_and_only_approved_buyers_open_them(): void
    {
        Storage::fake('local');
        $admin = User::factory()->superAdmin()->create();
        $service = $this->service();
        $plan = $service->plans()->create(['label' => 'PMP', 'price' => 2500, 'currency' => 'INR', 'status' => 'active']);

        $this->actingAs($admin)->get(route('admin.modules.index', 'services'))
            ->assertOk()
            ->assertSee(route('admin.services.materials', $service->id));
        $this->actingAs($admin)->get(route('admin.services.materials', $service->id))
            ->assertOk()
            ->assertSee('Service materials')
            ->assertSee('No PDF has been added for this service yet.');

        $this->actingAs($admin)->post(route('admin.services.pdfs.store', $service->id), [
            'title' => 'PDU claim guide',
            'description' => 'How to report PDUs.',
            'file' => UploadedFile::fake()->create('guide.pdf', 120, 'application/pdf'),
            'status' => 'active',
        ])->assertRedirect(route('admin.services.materials', $service->id));
        $this->actingAs($admin)->post(route('admin.services.videos.store', $service->id), [
            'title' => 'PDU walkthrough',
            'source_type' => 'upload',
            'file' => UploadedFile::fake()->create('walkthrough.mp4', 200, 'video/mp4'),
            'status' => 'active',
        ])->assertRedirect(route('admin.services.materials', $service->id));

        $pdf = CoursePdf::query()->sole();
        $video = CourseVideo::query()->sole();
        $this->assertSame($service->id, $pdf->service_id);
        $this->assertNull($pdf->course_id);
        $this->assertStringStartsWith('services/pdfs/', $pdf->file_path);
        $this->assertStringStartsWith('services/videos/', $video->file_path);
        Storage::disk('local')->assertExists([$pdf->file_path, $video->file_path]);

        $this->actingAs($admin)->get(route('admin.services.pdfs.edit', [$service->id, $pdf->id]))
            ->assertOk()
            ->assertSee(route('admin.services.pdfs.update', [$service->id, $pdf->id]), false)
            ->assertSee('Service materials');

        $buyer = User::factory()->create();
        $other = User::factory()->create();
        $order = $this->order($buyer, $plan->id);

        $this->actingAs($buyer)->get(route('account.services.index'))
            ->assertOk()
            ->assertSee('Waiting for approval')
            ->assertSee('Application Writing Support – PMP');
        $this->actingAs($buyer)->get(route('learn.services.show', $service))->assertForbidden();
        $this->actingAs($buyer)->get(route('learn.pdf', $pdf))->assertForbidden();

        $this->actingAs($admin)->post(route('admin.orders.approve', $order))->assertRedirect();

        $enrollment = ServiceEnrollment::query()->sole();
        $this->assertSame([$buyer->id, $service->id, $plan->id, $order->id, 'active'], [
            $enrollment->user_id, $enrollment->service_id, $enrollment->service_plan_id, $enrollment->order_id, $enrollment->status,
        ]);
        $this->assertSame(0, $buyer->enrollments()->count());

        $this->actingAs($buyer)->get(route('account.services.index'))
            ->assertOk()
            ->assertSee('Approved')
            ->assertSee(route('learn.services.show', $service), false);
        $this->actingAs($buyer)->get(route('learn.services.show', $service))
            ->assertOk()
            ->assertSee('Study Material')
            ->assertSee('PDU claim guide')
            ->assertSee(route('learn.pdf', $pdf), false)
            ->assertSee('PDU walkthrough')
            ->assertSee(route('learn.video', $video), false);
        $this->actingAs($buyer)->get(route('learn.pdf', $pdf))
            ->assertOk()
            ->assertSee('Back to service')
            ->assertSee(route('learn.services.show', $service), false);
        $this->actingAs($buyer)->get(route('learn.pdf.file', $pdf), ['X-Requested-With' => 'XMLHttpRequest', 'Sec-Fetch-Dest' => 'empty'])
            ->assertOk()
            ->assertHeader('Content-Type', 'application/pdf');
        $this->actingAs($buyer)->get(route('learn.video', $video), ['Sec-Fetch-Dest' => 'video'])->assertOk();
        $this->actingAs($buyer)->get(route('account.dashboard'))->assertOk()->assertSee('PDU claim guide');

        $this->actingAs($other)->get(route('learn.services.show', $service))->assertForbidden();
        $this->actingAs($other)->get(route('learn.pdf', $pdf))->assertForbidden();
        $this->actingAs($other)->get(route('learn.video', $video), ['Sec-Fetch-Dest' => 'video'])->assertForbidden();

        $service->update(['status' => 'inactive']);
        $this->actingAs($buyer)->get(route('learn.pdf', $pdf))->assertForbidden();

        $path = $pdf->file_path;
        $this->actingAs($admin)->delete(route('admin.services.pdfs.destroy', [$service->id, $pdf->id]))
            ->assertRedirect(route('admin.services.materials', $service->id));
        Storage::disk('local')->assertMissing($path);
    }

    public function test_a_course_enrollment_does_not_open_service_files(): void
    {
        $service = $this->service();
        $pdf = $service->pdfs()->create(['title' => 'Service notes', 'file_path' => 'services/pdfs/notes.pdf', 'status' => 'active']);
        $category = Category::query()->create(['name' => 'Project Management', 'slug' => 'project-management', 'status' => 'active']);
        $course = Course::query()->create([
            'category_id' => $category->id,
            'title' => 'PMP',
            'slug' => 'pmp-certification-training',
            'status' => 'active',
            'price' => 0,
            'price_on_request' => true,
        ]);
        $student = User::factory()->create();
        $student->enrollments()->create(['course_id' => $course->id, 'status' => 'active', 'enrolled_at' => now()]);

        $this->actingAs($student)->get(route('learn.pdf', $pdf))->assertForbidden();
        $this->actingAs($student)->get(route('account.services.index'))
            ->assertOk()
            ->assertSee('No services in this list yet.');
    }

    private function order(User $buyer, int $planId): Order
    {
        $this->actingAs($buyer)->post(route('cart.plans.store', $planId))->assertRedirect();
        $this->actingAs($buyer)->post(route('checkout.store'), [
            'name' => 'Asha Rao',
            'phone' => '9876543210',
            'country' => 'IN',
        ])->assertRedirect();

        return $buyer->orders()->sole();
    }

    private function service(): Service
    {
        return Service::query()->create([
            'title' => 'Application Writing Support',
            'slug' => 'application-writing-support',
            'short_description' => 'Help preparing a PMI application.',
            'sort_order' => 1,
            'status' => 'active',
        ]);
    }
}
