<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Course;
use App\Models\Service;
use App\Support\Content;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\TestCase;

class StaticContentTest extends TestCase
{
    use RefreshDatabase;

    public function test_content_reads_json_and_fills_site_settings(): void
    {
        $this->assertSame('Programs we run for enterprise teams.', Content::get('corporate.programs.title'));
        $this->assertContains('100+ corporate cohorts already delivered', Content::get('corporate.who.items'));
        $this->assertSame('fallback', Content::get('corporate.missing.key', 'fallback'));

        $this->expectException(RuntimeException::class);
        Content::get('no-such-page.title');
    }

    public function test_corporate_page_renders_programs_from_the_content_file(): void
    {
        Service::query()->create([
            'title' => 'Corporate Training',
            'slug' => 'corporate-training',
            'short_description' => 'Custom PMI-aligned training for teams.',
            'status' => 'active',
        ]);
        $category = Category::query()->create(['name' => 'Project Management', 'slug' => 'project-management', 'status' => 'active']);
        $course = Course::query()->create([
            'category_id' => $category->id,
            'title' => 'Project Management Professional (PMP)',
            'slug' => 'pmp-certification-training',
            'short_description' => 'PMP preparation.',
            'status' => 'active',
            'price' => 0,
            'price_on_request' => true,
        ]);

        $this->get(route('services.show', 'corporate-training'))
            ->assertOk()
            ->assertSeeInOrder(['Programs we run for enterprise teams.', 'PMO Uplift', 'Custom Programs'])
            ->assertSee('href="'.route('courses.show', $course).'"', false)
            ->assertSee('Training built for business outcomes.')
            ->assertSee('100+ corporate cohorts already delivered');

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Credentials that change the trajectory.')
            ->assertSee('The app behind certification success')
            ->assertSee('Enterprise PMO Uplift');
    }

    public function test_service_page_shows_why_cards_from_the_content_file(): void
    {
        $service = Service::query()->create([
            'title' => 'Application Writing Support',
            'slug' => 'application-writing-support',
            'short_description' => 'Help with the PMI application.',
            'status' => 'active',
        ]);
        $plain = Service::query()->create([
            'title' => 'Mentoring Hours',
            'slug' => 'mentoring-hours',
            'short_description' => 'One-to-one mentoring.',
            'description' => '<ul><li>Weekly calls with a trainer</li></ul>',
            'status' => 'active',
        ]);

        $this->get(route('services.show', $service))
            ->assertOk()
            ->assertSee('Why choose this program')
            ->assertSee('What makes our Application Writing Support different.')
            ->assertSeeInOrder(['svc-hero-strip', 'Application Review Support', 'Sample Applications', 'Step-by-Step Guide', '100% Money-Back', 'Why choose this program'], false)
            ->assertSeeInOrder(['100% Money-Back Assurance', 'Sample Approved Applications', 'Step-by-Step Writing Guide', 'Review Before Submission', 'Rewrite Support'])
            ->assertSeeInOrder(['Continue exploring', 'Other ways we accelerate your PMI journey.', 'Mentoring Hours', 'One-to-one mentoring.', 'Explore'])
            ->assertSee('href="'.route('services.show', $plain).'"', false);

        $this->get(route('services.show', $plain))
            ->assertOk()
            ->assertSee('What makes our Mentoring Hours different.')
            ->assertSee('Weekly calls with a trainer')
            ->assertDontSee('svc-hero-strip', false);
    }

    public function test_self_learning_sphere_shows_its_own_title(): void
    {
        $selfLearning = Service::query()->create([
            'title' => 'Self Learning Course',
            'slug' => 'self-learning-course',
            'short_description' => 'Learn your way.',
            'status' => 'active',
        ]);
        foreach (['PgMP', 'PfMP', 'RMP'] as $order => $label) {
            $selfLearning->plans()->create(['label' => $label, 'price' => 9999, 'currency' => 'INR', 'sort_order' => $order, 'status' => 'active']);
        }
        $other = Service::query()->create([
            'title' => 'Mentoring Hours',
            'slug' => 'mentoring-hours',
            'short_description' => 'One-to-one mentoring.',
            'status' => 'active',
        ]);

        $this->get(route('services.show', $selfLearning))
            ->assertOk()
            ->assertSeeInOrder(['svc-sphere-core svc-sphere-core-title', 'PgMP®', 'Self-Learning Course', 'Learn. Apply. Lead.'], false)
            ->assertSeeInOrder(['svc-hero-strip', 'Expert Trainer Sessions', 'Latest Standard', 'Exam-Aligned Content', 'Examples &amp; Case Studies', 'Why choose this program'], false)
            ->assertDontSee('Join the elite');

        $this->get(route('services.show', $selfLearning).'?plan=rmp')
            ->assertOk()
            ->assertSeeInOrder(['svc-sphere-core svc-sphere-core-title', '<strong>RMP®</strong>', 'Self-Learning Course'], false)
            ->assertDontSee('<strong>PgMP®</strong>', false);

        $this->get(route('services.show', $selfLearning).'?plan=PMP')
            ->assertOk()
            ->assertSeeInOrder(['svc-sphere-core svc-sphere-core-title', 'PgMP®'], false);

        $this->get(route('services.show', $other))
            ->assertOk()
            ->assertSee('Join the elite')
            ->assertDontSee('Learn. Apply. Lead.')
            ->assertDontSee('svc-hero-features', false);
    }

    public function test_certification_advisory_hero_shows_published_stats(): void
    {
        $advisory = Service::query()->create(['title' => 'Certification Advisory', 'slug' => 'certification-advisory', 'short_description' => 'A 30-minute consultation.', 'status' => 'active']);
        $advisory->plans()->create(['label' => 'One to One', 'price' => 1500, 'compare_price' => 2550, 'currency' => 'INR', 'sort_order' => 1, 'status' => 'active']);

        $this->get(route('services.show', $advisory))
            ->assertOk()
            ->assertSeeInOrder(['hero-actions', 'svc-hero-stats', '60 mins', 'Dedicated Advisory Session', '5+', 'PMI Certifications Covered', '100%', 'Personalized Certification Roadmap', 'Same Week', 'Available Appointments'], false)
            ->assertSeeInOrder(['class="journey"', '01', 'Understand your profile', '02', 'Expert eligibility assessment', '03', 'Right certification recommendation', '04', 'Personalized Roadmap', '05', 'Faster Path to certification &amp; career growth', 'journey-needle'], false)
            ->assertDontSee('svc-sphere', false);
    }

    public function test_pdu_course_hero_shows_its_stats(): void
    {
        $pdu = Service::query()->create(['title' => 'PDU Course', 'slug' => 'pdu-course', 'short_description' => 'Earn PDUs.', 'status' => 'active']);

        $this->get(route('services.show', $pdu))
            ->assertOk()
            ->assertSeeInOrder(['hero-actions', 'svc-hero-stats', '60', 'PMI-approved PDUs', '3 months', 'Self-paced access', '100%', 'Approval-assured', 'PMI-ATP', 'Authorized provider'], false)
            ->assertSeeInOrder(['talent-card', 'Earn all 60 PDUs in one program', 'Aligned to the PMI Talent Triangle', 'Ways of Working', 'Power Skills', 'Business Acumen', 'Self-Paced', 'Renewal Support', 'Expert-Led'], false)
            ->assertDontSee('svc-sphere', false);
    }

    public function test_mobile_services_menu_groups_plans_under_each_service(): void
    {
        $writing = Service::query()->create(['title' => 'Application Writing Support', 'slug' => 'application-writing-support', 'short_description' => 'Writing help.', 'status' => 'active', 'sort_order' => 1]);
        $pdu = Service::query()->create(['title' => 'PDU Course', 'slug' => 'pdu-course', 'short_description' => 'Earn PDUs.', 'status' => 'active', 'sort_order' => 2]);
        $writing->plans()->create(['label' => 'PMP', 'price' => 2500, 'currency' => 'INR', 'sort_order' => 1, 'status' => 'active']);
        $writing->plans()->create(['label' => 'PgMP', 'price' => 6000, 'currency' => 'INR', 'sort_order' => 2, 'status' => 'active']);
        $pdu->plans()->create(['label' => 'PDU', 'price' => 4999, 'currency' => 'INR', 'sort_order' => 1, 'status' => 'active']);

        $this->get(route('services.index'))
            ->assertOk()
            ->assertSeeInOrder([
                'nav-sub nav-sub-services',
                'nav-sub-label">Application Writing Support',
                'href="'.route('services.show', $writing).'?plan=PMP">PMP® - Application Writing Support',
                'href="'.route('services.show', $writing).'?plan=PgMP">PgMP® - Application Writing Support',
                'class="nav-sub-single" href="'.route('services.show', $pdu).'">PDU Course',
            ], false)
            ->assertDontSee('PDU® - PDU Course');
    }

    public function test_exam_simulation_hero_shows_the_selected_plan_panel(): void
    {
        $simulation = Service::query()->create([
            'title' => 'Exam Simulation Tool',
            'slug' => 'exam-simulation-tool',
            'short_description' => 'Question banks across the exam domains.',
            'status' => 'active',
        ]);
        $simulation->plans()->create(['label' => 'PMP', 'price' => 6999, 'currency' => 'INR', 'sort_order' => 1, 'status' => 'active', 'description' => '30 Days']);
        $simulation->plans()->create(['label' => 'PgMP', 'price' => 9999, 'currency' => 'INR', 'sort_order' => 2, 'status' => 'active']);

        $this->get(route('services.show', $simulation).'?plan=PMP')
            ->assertOk()
            ->assertSee('svc-page-hero', false)
            ->assertSeeInOrder(['svc-hero-features', 'Exam-Style Questions', 'Domain Coverage', 'Detailed Rationale', 'Trainer-Verified Answers'], false)
            ->assertSeeInOrder(['sim-panel', 'PMP®', 'Exam Simulation Tool', 'Simulate. Strengthen. Succeed.', 'PMP® Practice Question'], false)
            ->assertSeeInOrder(['Your package', 'INR</small> 6,999', 'Access: 30 Days'], false)
            ->assertSee('href="'.route('services.show', $simulation).'?plan=PgMP"', false)
            ->assertDontSee('svc-sphere-core', false);

        $this->get(route('services.show', $simulation).'?plan=pgmp')
            ->assertOk()
            ->assertSeeInOrder(['sim-panel', 'PgMP®', 'PgMP® Practice Question', 'Your package', 'INR</small> 9,999'], false)
            ->assertDontSee('Access:');
    }

    public function test_service_pricing_uses_a_package_card_or_side_by_side_cards(): void
    {
        $writing = Service::query()->create([
            'title' => 'Application Writing Support',
            'slug' => 'application-writing-support',
            'short_description' => 'Help with the PMI application.',
            'status' => 'active',
        ]);
        $pmp = $writing->plans()->create(['label' => 'PMP', 'price' => 2500, 'compare_price' => 3500, 'currency' => 'INR', 'sort_order' => 1, 'status' => 'active']);
        $writing->plans()->create(['label' => 'PgMP', 'price' => 6000, 'currency' => 'INR', 'sort_order' => 2, 'status' => 'active']);

        $this->get(route('services.show', $writing))
            ->assertOk()
            ->assertSee('Professional support &amp; transparent pricing.', false)
            ->assertSee('role="tablist"', false)
            ->assertSee('PMP Application Writing Support')
            ->assertSeeInOrder(['INR</small> 2,500', 'INR 3,500', '29% OFF'], false)
            ->assertSeeInOrder(['100% Money-Back Assurance', 'Sample Approved Applications', 'Step-by-Step Writing Guide', 'Review Before Submission'])
            ->assertSee('action="'.route('cart.plans.store', $pmp->id).'"', false)
            ->assertSee('Prices in INR · Orders are confirmed after approval')
            ->assertDontSee('svc-tier-grid', false);

        $simulation = Service::query()->create([
            'title' => 'Exam Simulation Tool',
            'slug' => 'exam-simulation-tool',
            'short_description' => 'Question banks across the exam domains.',
            'status' => 'active',
        ]);
        foreach (['PMP' => 6999, 'PgMP' => 9999, 'PfMP' => 11999] as $label => $price) {
            $simulation->plans()->create(['label' => $label, 'price' => $price, 'currency' => 'INR', 'sort_order' => $price, 'status' => 'active', 'description' => $label === 'PgMP' ? '60 days access' : null]);
        }

        $this->get(route('services.show', $simulation))
            ->assertOk()
            ->assertSee('svc-tier-grid svc-tier-grid-3', false)
            ->assertSeeInOrder(['PMP', 'INR</small> 6,999', 'PgMP', '60 days access', 'INR</small> 9,999', 'PfMP', 'INR</small> 11,999'], false)
            ->assertDontSee('role="tablist"', false);
    }
}
