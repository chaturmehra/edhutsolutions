<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Course;
use App\Models\CourseResource;
use App\Models\Lesson;
use App\Models\Trainer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CourseDetailsTest extends TestCase
{
    use RefreshDatabase;

    public function test_course_page_shows_the_detail_tabs_above_the_trainers(): void
    {
        $course = $this->course([
            'overview' => "First overview paragraph.\n\nSecond overview paragraph.",
            'learning_objectives' => "Align programs with strategy\n\nManage interdependent projects",
        ]);
        $module = $course->sections()->create(['title' => 'Program Governance', 'sort_order' => 1, 'status' => 'active']);
        $module->lessons()->create(['title' => 'Governance Structures and Oversight', 'slug' => 'gso', 'sort_order' => 1, 'status' => 'active']);
        $module->lessons()->create(['title' => 'Hidden topic', 'slug' => 'hidden', 'sort_order' => 2, 'status' => 'inactive']);
        $hidden = $course->sections()->create(['title' => 'Draft module', 'sort_order' => 2, 'status' => 'inactive']);
        $hidden->lessons()->create(['title' => 'Draft topic', 'slug' => 'draft', 'sort_order' => 1, 'status' => 'active']);
        $course->resources()->create(['title' => 'PgMP Certification Handbook', 'description' => 'Application process and exam fees.', 'file_path' => 'courses/resources/handbook.pdf', 'status' => 'active']);
        $guide = $course->resources()->create(['title' => 'Strategic Mindset Guide', 'status' => 'active']);
        $course->resources()->create(['title' => 'Unpublished resource', 'status' => 'inactive']);
        $trainer = Trainer::query()->create(['name' => 'Asha Mentor', 'status' => 'active']);
        $trainer->courses()->sync([$course->id]);

        $response = $this->get(route('courses.show', $course))->assertOk();
        $response->assertSeeInOrder([
            'Course details', 'What you will master in the PgMP® course.',
            'Overview', 'Learning Objectives', 'Agenda', 'Resources',
            'First overview paragraph.', 'Second overview paragraph.',
            'Align programs with strategy', 'Manage interdependent projects',
            'Module 1', 'Program Governance', 'Governance Structures and Oversight',
            'PgMP Certification Handbook', 'Application process and exam fees.', 'Strategic Mindset Guide',
            'Asha Mentor',
        ]);
        $response->assertSee('role="tablist"', false)
            ->assertSee('id="crs-panel-agenda" aria-labelledby="crs-tab-agenda" tabindex="0"  hidden', false)
            ->assertSee('href="'.route('courses.resource', [$course, $course->resources()->first()->id]).'"', false)
            ->assertDontSee('href="'.route('courses.resource', [$course, $guide->id]).'"', false)
            ->assertDontSee('Hidden topic')
            ->assertDontSee('Draft module')
            ->assertDontSee('Unpublished resource');
    }

    public function test_tabs_without_content_are_left_out(): void
    {
        $course = $this->course(['overview' => 'Only an overview.']);

        $this->get(route('courses.show', $course))
            ->assertSee('Only an overview.')
            ->assertSee('id="crs-tab-overview"', false)
            ->assertDontSee('id="crs-tab-objectives"', false)
            ->assertDontSee('id="crs-tab-agenda"', false)
            ->assertDontSee('id="crs-tab-resources"', false);

        $course->update(['overview' => null]);
        $this->get(route('courses.show', $course))->assertDontSee('Course details');
    }

    public function test_section_bar_links_only_to_sections_on_the_page(): void
    {
        $course = $this->course(['overview' => 'Only an overview.']);
        $trainer = Trainer::query()->create(['name' => 'Asha Mentor', 'status' => 'active']);
        $trainer->courses()->sync([$course->id]);

        $response = $this->get(route('courses.show', $course))->assertOk();
        $response->assertSeeInOrder([
            'aria-label="Course sections"',
            'href="#why-edhut">Why Us',
            'href="#cohorts">Schedule',
            'href="#course-details">Course Details',
            'href="#trainers">Trainers',
            'href="#enroll">Enroll',
            'id="why-edhut"',
        ], false);
        $response->assertDontSee('href="#packages"', false)
            ->assertDontSee('href="#hall-of-fame"', false)
            ->assertDontSee('href="#faqs"', false);
    }

    public function test_admin_saves_overview_and_objectives_on_the_course_form(): void
    {
        $course = $this->course();
        $admin = User::factory()->superAdmin()->create();

        $this->actingAs($admin)->get(route('admin.modules.edit', ['courses', $course->id]))
            ->assertOk()
            ->assertSee('Course details: Overview tab')
            ->assertSee('Course details: Learning objectives tab');

        $this->actingAs($admin)->put(route('admin.modules.update', ['courses', $course->id]), [
            'category_id' => $course->category_id,
            'title' => $course->title,
            'slug' => $course->slug,
            'overview' => 'New overview.',
            'learning_objectives' => "One\nTwo",
            'status' => 'active',
        ])->assertSessionHasNoErrors();

        $course->refresh();
        $this->assertSame('New overview.', $course->overview);
        $this->assertSame(['One', 'Two'], $course->objectiveList());
    }

    public function test_rich_editor_loads_only_for_html_fields_and_keeps_the_markup(): void
    {
        $course = $this->course(['description' => '<h2>Why PgMP</h2><ul><li>Mock exams</li></ul>']);
        $admin = User::factory()->superAdmin()->create();

        $this->actingAs($admin)->get(route('admin.modules.edit', ['courses', $course->id]))
            ->assertOk()
            ->assertSee('assets/js/vendor/jodit.min.js', false)
            ->assertSee('<label class="editor-label" for="field-description">Description</label>', false)
            ->assertSee('name="description" data-editor>&lt;h2&gt;Why PgMP&lt;/h2&gt;&lt;ul&gt;&lt;li&gt;Mock exams&lt;/li&gt;&lt;/ul&gt;</textarea>', false)
            ->assertDontSee('name="short_description" data-editor', false);

        $this->actingAs($admin)->put(route('admin.modules.update', ['courses', $course->id]), [
            'category_id' => $course->category_id,
            'title' => $course->title,
            'description' => '<h2>Why PgMP</h2><table><tr><td>Mock exams</td></tr></table>',
            'status' => 'active',
        ])->assertSessionHasNoErrors();
        $this->assertSame('<h2>Why PgMP</h2><table><tr><td>Mock exams</td></tr></table>', $course->refresh()->description);

        $quiz = $course->quizzes()->create(['title' => 'Check', 'slug' => 'check', 'status' => 'active']);
        $this->actingAs($admin)->get(route('admin.modules.edit', ['quizzes', $quiz->id]))
            ->assertOk()
            ->assertDontSee('data-editor', false)
            ->assertDontSee('jodit.min.js', false);
    }

    public function test_admin_edits_agenda_modules_and_keeps_existing_topic_ids(): void
    {
        $course = $this->course();
        $admin = User::factory()->superAdmin()->create();

        $this->actingAs($admin)->get(route('admin.courses.materials', $course->id))
            ->assertOk()
            ->assertSee('Course details: Agenda')
            ->assertSee('Course details: Resources');

        $this->actingAs($admin)->post(route('admin.courses.modules.store', $course->id), [
            'title' => 'Benefits Management',
            'topics' => "Identify and Define Benefits\r\n\r\nRealize and Sustain Benefit\r\nMonitor and Improve Benefits",
            'sort_order' => 1,
            'status' => 'active',
        ])->assertRedirect(route('admin.courses.materials', $course->id));

        $module = $course->sections()->sole();
        $this->assertSame(['Identify and Define Benefits', 'Realize and Sustain Benefit', 'Monitor and Improve Benefits'], $module->lessons()->pluck('title')->all());
        $firstId = $module->lessons()->first()->id;

        $this->actingAs($admin)->get(route('admin.courses.modules.edit', [$course->id, $module->id]))
            ->assertOk()
            ->assertSee("Identify and Define Benefits\nRealize and Sustain Benefit\nMonitor and Improve Benefits");

        $this->actingAs($admin)->put(route('admin.courses.modules.update', [$course->id, $module->id]), [
            'title' => 'Benefits Management',
            'topics' => "Identify Benefits\nSustaining Business Value",
            'status' => 'active',
        ])->assertSessionHasNoErrors();

        $this->assertSame(['Identify Benefits', 'Sustaining Business Value'], $module->lessons()->pluck('title')->all());
        $this->assertSame($firstId, $module->lessons()->first()->id);
        $this->assertSame(1, Lesson::onlyTrashed()->count());

        $this->actingAs($admin)->post(route('admin.courses.modules.store', $course->id), [
            'title' => 'Empty',
            'topics' => '',
            'status' => 'active',
        ])->assertSessionHasErrors('topics');
    }

    public function test_resource_pdf_is_uploaded_and_opens_only_while_active(): void
    {
        Storage::fake('local');
        $course = $this->course();
        $admin = User::factory()->superAdmin()->create();

        $this->actingAs($admin)->post(route('admin.courses.resources.store', $course->id), [
            'title' => 'PgMP Examination Content Outline (ECO)',
            'description' => 'Program management tasks by domain.',
            'file' => UploadedFile::fake()->create('PgMP ECO.pdf', 120, 'application/pdf'),
            'status' => 'active',
        ])->assertSessionHasNoErrors();

        $resource = CourseResource::query()->sole();
        $this->assertStringStartsWith('courses/resources/', $resource->file_path);
        Storage::disk('local')->assertExists($resource->file_path);

        auth()->logout();
        $this->get(route('courses.resource', [$course, $resource->id]))
            ->assertOk()
            ->assertHeader('Content-Type', 'application/pdf');

        $resource->update(['status' => 'inactive']);
        $this->get(route('courses.resource', [$course, $resource->id]))->assertNotFound();

        $this->actingAs($admin)->put(route('admin.courses.resources.update', [$course->id, $resource->id]), [
            'title' => $resource->title,
            'remove_file' => '1',
            'status' => 'active',
        ])->assertSessionHasNoErrors();
        $path = $resource->file_path;
        $resource->refresh();
        $this->assertNull($resource->file_path);
        Storage::disk('local')->assertMissing($path);
        $this->get(route('courses.resource', [$course, $resource->id]))->assertNotFound();

        $this->actingAs($admin)->post(route('admin.courses.resources.store', $course->id), [
            'title' => 'Not a PDF',
            'file' => UploadedFile::fake()->image('photo.jpg'),
            'status' => 'active',
        ])->assertSessionHasErrors('file');
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function course(array $attributes = []): Course
    {
        $category = Category::query()->firstOrCreate(
            ['slug' => 'project-management'],
            ['name' => 'Project Management', 'status' => 'active'],
        );

        return Course::query()->create($attributes + [
            'category_id' => $category->id,
            'title' => 'Program Management Professional (PgMP)',
            'slug' => 'pgmp-certification-training',
            'short_description' => 'Preparation.',
            'status' => 'active',
            'price' => 0,
            'price_on_request' => true,
        ]);
    }
}
