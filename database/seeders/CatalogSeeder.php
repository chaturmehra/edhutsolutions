<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Course;
use App\Models\Quiz;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CatalogSeeder extends Seeder
{
    public function run(): void
    {
        $category = Category::query()->updateOrCreate(['slug' => 'project-management'], [
            'name' => 'Project Management',
            'description' => 'PMI certification programs taught by PMI Authorized Trainers.',
            'sort_order' => 1,
            'status' => 'active',
        ]);

        $pmp = $this->course($category->id, [
            'title' => 'Project Management Professional (PMP)',
            'slug' => 'pmp-certification-training',
            'short_description' => 'PMP is the gold standard in project management certification and distinguishes you from other project professionals.',
            'description' => '<p>Showcase your capability to lead projects across industries with this globally respected certification. The Project Management Professional (PMP) certification recognizes individuals proficient in managing people, processes, and business objectives in professional project environments.</p><p>PMI Authorized Training Partners are more than trainers. They guide you through the latest PMI Global Standards.</p><h2>Why PMP with EdHut</h2><ul><li>100% application approval assistance</li><li>7 full-length mock exams</li><li>PMI Authorized Training Partner</li><li>PMI Authorized PMP Instructor</li><li>PMI approved study material</li><li>PMI pre-approved PDUs</li><li>Advanced learning management system</li><li>Continuous learning support</li></ul>',
            'thumbnail' => 'assets/images/courses/pmp.webp',
            'thumbnail_alt' => 'Project Management Professional certification badge',
            'duration' => '36 hours',
            'level' => 'Professional',
        ], [
            ['title' => 'Creating a High-Performing Team', 'lessons' => ['Build a Team', 'Define Team Ground Rules', 'Negotiate Project Agreements', 'Empower Team Members and Stakeholders', 'Train Team Members and Stakeholders', 'Engage and Support Virtual Teams', 'Build a Shared Understanding about a Project']],
            ['title' => 'Starting the Project', 'lessons' => ['Determine Appropriate Project Methodology', 'Plan and Manage Scope', 'Plan and Manage Budget and Resources', 'Plan and Manage Schedule', 'Plan and Manage Quality', 'Integrate Project Planning Activities', 'Plan and Manage Procurement', 'Establish Project Governance Structure', 'Plan and Manage Project Closure']],
            ['title' => 'Doing the Work', 'lessons' => ['Assess and Manage Risks', 'Execute the Project to Deliver Business Value', 'Manage Communications', 'Engage Stakeholders', 'Create Project Artifacts', 'Manage Project Changes', 'Manage Project Issues', 'Ensure Knowledge Transfer']],
            ['title' => 'Keeping the Team on Track', 'lessons' => ['Lead a Team', 'Support Team Performance', 'Address and Remove Impediments', 'Manage Conflict', 'Collaborate with Stakeholders', 'Mentor Relevant Stakeholders', 'Apply Emotional Intelligence']],
            ['title' => 'Keeping the Business in Mind', 'lessons' => ['Manage Compliance Requirements', 'Evaluate and Deliver Project Benefits and Value', 'Evaluate Business Environment Changes', 'Support Organizational Change', 'Employ Continuous Process Improvement']],
        ], [
            ['2026-10-10', '2026-10-18', '9:30 AM to 6:30 PM (IST)', '36 hrs - 4 sessions'],
            ['2026-09-26', '2026-10-04', '9:30 AM to 6:30 PM (IST)', '36 hrs - 4 sessions'],
        ], [
            ['What is the PMP certification, and who should pursue it?', 'The PMP certification is a globally recognized credential for project managers. It is aimed at professionals who want to advance in project management.'],
            ['What are the prerequisites for applying for the PMP certification?', 'Candidates need 36 months of project management experience with a four-year degree, or 60 months with a high school diploma or associate degree, plus 35 hours of project management education.'],
            ['What is the cost of the PMP exam?', 'The published EdHut page lists the PMP exam fee as $405 for PMI members and $555 for non-members.'],
            ['How many questions are on the PMP exam?', 'The PMP exam has 180 questions. You have 230 minutes to complete it, with two optional 10-minute breaks.'],
            ['What topics are covered in the PMP exam?', 'The exam covers three domains: People, Process, and Business Environment.'],
            ['How long is the PMP certification valid?', 'PMP certification is valid for three years. Maintaining it requires 60 PDUs in that period.'],
        ]);

        $this->course($category->id, [
            'title' => 'Program Management Professional (PgMP)',
            'slug' => 'pgmp-certification-training',
            'short_description' => 'The PgMP certification showcases advanced expertise in managing multiple related projects in a coordinated and strategic manner.',
            'description' => '<p>This program is designed to give participants a working knowledge of PMI program management practices and to prepare them for the PgMP certification exam. Concepts are explained with case studies, tools, and templates, and the engagement continues through certification.</p><h2>Why PgMP with EdHut</h2><ul><li>Application writing support</li><li>Practice tests and mock exams</li><li>PMI Authorized Training Partner</li><li>Advanced learning management system</li><li>Continuous learning support</li><li>Personalized learning paths</li><li>PMI pre-approved PDUs</li></ul><h2>Who should attend</h2><p>Project managers, senior managers, functional managers, program managers, and PMO consultants who support or manage work that spans functions, organisations, clients, and regions.</p>',
            'thumbnail' => 'assets/images/courses/pgmp.webp',
            'thumbnail_alt' => 'Program Management Professional certification badge',
            'duration' => '24 hours',
            'level' => 'Advanced',
        ], [
            ['title' => 'Program Foundations', 'lessons' => ['PMI Standard of Program Management', 'Project, Program, and Portfolio', 'Strategy, Operations, and Programs', 'Role of the Program Manager', 'Program Manager Skills and Competencies', 'Governance Board and PMO']],
            ['title' => 'Program Lifecycle', 'lessons' => ['Performance Domains', 'Program Life-Cycle Phases', 'Strategic Alignment', 'Program Mandate and Business Case', 'Program Plan and Roadmap', 'Definition, Benefits Delivery, and Closure']],
            ['title' => 'Benefits, Governance, and Stakeholders', 'lessons' => ['Benefits Lifecycle', 'Benefit Dependency Map', 'Benefits Register and Realization Plan', 'Governance Structure and Roles', 'Issue Escalation and Quality', 'Stakeholder Identification and Engagement']],
        ], [
            ['2026-07-04', '2026-07-12', '10:00 AM - 3:00 PM (IST)', '24 hours - 4 sessions'],
            ['2026-09-12', '2026-09-20', '10:00 AM - 3:00 PM (IST)', '24 hours - 4 sessions'],
            ['2026-11-21', '2026-11-29', '10:00 AM to 3:00 PM (IST)', '24 hours - 4 sessions'],
        ], [
            ['How does PgMP differ from PMP?', 'PgMP focuses on managing multiple related projects as a program. PMP is centered on individual project management.'],
            ['Is PMP required before PgMP?', 'No. A PMP is not mandatory. Applicants must meet PMI program management experience requirements.'],
            ['What does the PgMP exam cover?', 'Five domains: Strategic Program Management, Program Lifecycle, Benefits Management, Stakeholder Engagement, and Governance.'],
            ['What is the cost of the PgMP exam?', 'The published EdHut page lists $800 for PMI members and $1,000 for non-members.'],
        ]);

        $this->course($category->id, [
            'title' => 'Portfolio Management Professional (PfMP)',
            'slug' => 'pfmp-certification-training',
            'short_description' => 'The PfMP certification affirms the ability to manage and align projects, programs, and operations with strategic objectives.',
            'description' => '<p>Orchestrate the success of project portfolios with precision. As a certified PfMP, you balance priorities, watch portfolio performance, and direct resources to where they deliver the greatest impact.</p><h2>Why PfMP with EdHut</h2><ul><li>Application writing support</li><li>Practice tests and mock exams</li><li>Study materials aligned to the latest standard</li><li>PMI Authorized Training Partner</li><li>Advanced learning management system</li><li>Continuous learning support</li><li>PMI pre-approved PDUs</li></ul><p>The public PfMP page repeats a block of PgMP body copy. This record uses the PfMP headline, the PfMP benefits list, and the PfMP exam domains published on that page.</p>',
            'thumbnail' => 'assets/images/courses/pfmp.webp',
            'thumbnail_alt' => 'Portfolio Management Professional certification badge',
            'duration' => 'Portfolio management',
            'level' => 'Advanced',
        ], [
            ['title' => 'PfMP Domains', 'lessons' => ['Strategic Alignment', 'Governance', 'Portfolio Performance', 'Portfolio Risk Management', 'Communications Management']],
            ['title' => 'Portfolio Practice', 'lessons' => ['Align the Portfolio to Strategy', 'Balance and Diversify the Portfolio', 'Establish Portfolio Governance', 'Realize Portfolio Value', 'Manage Portfolio Change', 'Engage Stakeholders']],
        ], [], [
            ['Who is the PfMP certification for?', 'Professionals who manage portfolios of projects and programs and align them with organisational strategy.'],
            ['What experience is required?', 'The published page describes a minimum of 8 years of business experience and 4 years of portfolio management experience. Requirements vary by education. Confirm the current PMI PfMP Handbook.'],
            ['How is PfMP different from PMP and PgMP?', 'PMP focuses on projects, PgMP on programs, and PfMP on a portfolio of projects and programs aligned to strategy.'],
            ['What does the PfMP exam cover?', 'Five domains: Strategic Alignment, Governance, Portfolio Performance, Portfolio Risk Management, and Communications Management. The published page lists 170 multiple-choice questions and 4 hours.'],
        ]);

        $this->course($category->id, [
            'title' => 'Risk Management Professional (PMI-RMP)',
            'slug' => 'pmi-rmp-certification-training',
            'short_description' => 'The PMI-RMP credential highlights your ability to identify and assess project risks, mitigate threats, and capitalise on opportunities.',
            'description' => '<p>Transform risks into opportunities. Earning the PMI-RMP equips you to identify issues early, apply core risk principles, and drive better project results.</p><p>The published introduction notes that high-performing organisations practice risk management more often, meet goals more often, and waste less money than low performers, citing PMI Pulse of the Profession.</p><h2>Why PMI-RMP with EdHut</h2><ul><li>Application writing support</li><li>Practice tests and mock exams</li><li>Study materials aligned to the latest standard</li><li>PMI Authorized Training Partner</li><li>Advanced learning management system</li><li>Continuous learning support</li><li>PMI pre-approved PDUs</li></ul>',
            'thumbnail' => 'assets/images/courses/rmp.webp',
            'thumbnail_alt' => 'PMI Risk Management Professional certification badge',
            'duration' => 'Risk management',
            'level' => 'Professional',
        ], [
            ['title' => 'Risk Strategy and Planning', 'lessons' => ['Preliminary Document Analysis', 'Assess the Project Environment', 'Confirm Risk Thresholds', 'Establish the Risk Management Strategy', 'Document the Risk Management Plan']],
            ['title' => 'Risk Identification', 'lessons' => ['Conduct Risk Identification', 'Examine Assumptions and Constraints', 'Document Triggers and Thresholds', 'Develop the Risk Register']],
            ['title' => 'Risk Analysis', 'lessons' => ['Perform Qualitative Analysis', 'Perform Quantitative Analysis', 'Identify Threats and Opportunities']],
            ['title' => 'Risk Response', 'lessons' => ['Plan Risk Responses', 'Implement Risk Responses']],
            ['title' => 'Monitor and Close Risks', 'lessons' => ['Gather and Analyse Performance Data', 'Monitor Residual and Secondary Risks', 'Update Project Documents', 'Monitor Project Risk Levels']],
        ], [], [
            ['Who is PMI-RMP for?', 'Professionals who specialise in identifying, assessing, and managing project risks.'],
            ['What are the published eligibility paths?', 'A secondary degree with 36 months of project risk management experience in the last 5 years and 40 hours of risk education, or a four-year degree with 24 months of experience and 30 hours of education. The same page also lists hour-based figures. Confirm the current PMI-RMP Handbook.'],
            ['What does the exam cover?', 'Five domains: Risk Strategy and Planning, Risk Identification, Risk Analysis, Risk Response, and Monitor and Close Risks. The page lists 115 multiple-choice questions and 150 minutes.'],
            ['What is the exam fee listed on the page?', '$520 for PMI members and $670 for non-members.'],
        ]);

        $this->seedQuiz($pmp);
        $this->seedNotes($pmp);
    }

    private function course(int $categoryId, array $attributes, array $sections, array $schedules, array $faqs): Course
    {
        $course = Course::query()->updateOrCreate(['slug' => $attributes['slug']], $attributes + [
            'category_id' => $categoryId,
            'price' => 0,
            'currency' => 'INR',
            'price_on_request' => true,
            'language' => 'English',
            'is_featured' => true,
            'status' => 'active',
        ]);

        if (blank($course->overview) && preg_match('#<p>(.*?)</p>#s', (string) $course->description, $intro)) {
            $course->update(['overview' => trim(html_entity_decode(strip_tags($intro[1]), ENT_QUOTES | ENT_HTML5))]);
        }

        if ($course->sections()->count() === 0) {
            foreach ($sections as $index => $section) {
                $model = $course->sections()->create([
                    'title' => $section['title'],
                    'sort_order' => $index + 1,
                    'status' => 'active',
                ]);
                foreach ($section['lessons'] as $lessonIndex => $title) {
                    $model->lessons()->create([
                        'title' => $title,
                        'slug' => Str::slug($title).'-'.($lessonIndex + 1),
                        'sort_order' => $lessonIndex + 1,
                        'status' => 'active',
                    ]);
                }
            }
        }

        if ($course->schedules()->count() === 0) {
            foreach ($schedules as $schedule) {
                $course->schedules()->create([
                    'starts_on' => $schedule[0],
                    'ends_on' => $schedule[1],
                    'time_label' => $schedule[2],
                    'hours_label' => $schedule[3],
                    'status' => 'active',
                ]);
            }
        }

        if ($course->faqs()->count() === 0) {
            foreach ($faqs as $index => $faq) {
                $course->faqs()->create([
                    'question' => $faq[0],
                    'answer' => $faq[1],
                    'sort_order' => $index + 1,
                    'status' => 'active',
                ]);
            }
        }

        return $course;
    }

    private function seedQuiz(Course $course): void
    {
        $quiz = Quiz::query()->updateOrCreate(['slug' => 'pmp-orientation-check'], [
            'course_id' => $course->id,
            'title' => 'PMP Orientation Check',
            'description' => 'A short check built from facts published on the EdHut PMP page. It is not a PMI exam paper.',
            'negative_marking' => 0,
            'pass_percentage' => 70,
            'time_limit_minutes' => 10,
            'status' => 'active',
        ]);

        if ($quiz->questions()->count() > 0) {
            return;
        }

        $single = $quiz->questions()->create([
            'type' => 'single',
            'prompt' => 'How many questions does the published EdHut page say are on the PMP exam?',
            'marks' => 1,
            'negative_marks' => 0,
            'explanation' => 'The PMP page states that the exam has 180 questions and 230 minutes.',
            'sort_order' => 1,
            'status' => 'active',
        ]);
        foreach (['100', '180', '200', '120'] as $index => $option) {
            $single->options()->create([
                'option_text' => $option,
                'is_correct' => $option === '180',
                'sort_order' => $index + 1,
            ]);
        }

        $multi = $quiz->questions()->create([
            'type' => 'multiple',
            'prompt' => 'Which domains does the published PMP page say the exam covers? Select every correct domain.',
            'marks' => 2,
            'negative_marks' => 0,
            'explanation' => 'The page names three domains: People, Process, and Business Environment.',
            'sort_order' => 2,
            'status' => 'active',
        ]);
        foreach ([
            ['People', true],
            ['Process', true],
            ['Business Environment', true],
            ['Only procurement calculations', false],
        ] as $index => $option) {
            $multi->options()->create([
                'option_text' => $option[0],
                'is_correct' => $option[1],
                'sort_order' => $index + 1,
            ]);
        }
    }

    private function seedNotes(Course $course): void
    {
        if ($course->pdfs()->exists()) {
            return;
        }

        $path = 'courses/pmp-orientation-notes.pdf';
        Storage::disk('local')->put($path, $this->simplePdf('EdHut PMP orientation notes. The published page lists 180 questions and 230 minutes.'));
        $course->pdfs()->create([
            'title' => 'PMP orientation notes',
            'file_path' => $path,
            'original_name' => 'pmp-orientation-notes.pdf',
            'file_size' => Storage::disk('local')->size($path),
            'sort_order' => 1,
            'status' => 'active',
        ]);
    }

    private function simplePdf(string $text): string
    {
        $safe = str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $text);
        $stream = "BT /F1 12 Tf 40 140 Td ({$safe}) Tj ET";
        $objects = [
            "1 0 obj<</Type/Catalog/Pages 2 0 R>>endobj\n",
            "2 0 obj<</Type/Pages/Count 1/Kids[3 0 R]>>endobj\n",
            "3 0 obj<</Type/Page/Parent 2 0 R/MediaBox[0 0 500 220]/Contents 4 0 R/Resources<</Font<</F1 5 0 R>>>>>>endobj\n",
            "4 0 obj<</Length ".strlen($stream).">>stream\n{$stream}\nendstream\nendobj\n",
            "5 0 obj<</Type/Font/Subtype/Type1/BaseFont/Helvetica>>endobj\n",
        ];
        $pdf = "%PDF-1.4\n";
        $offsets = [];
        foreach ($objects as $object) {
            $offsets[] = strlen($pdf);
            $pdf .= $object;
        }
        $xref = strlen($pdf);
        $pdf .= "xref\n0 ".(count($objects) + 1)."\n";
        $pdf .= "0000000000 65535 f \n";
        foreach ($offsets as $offset) {
            $pdf .= sprintf("%010d 00000 n \n", $offset);
        }
        $pdf .= 'trailer<</Size '.(count($objects) + 1)."/Root 1 0 R>>\nstartxref\n{$xref}\n%%EOF";

        return $pdf;
    }
}
