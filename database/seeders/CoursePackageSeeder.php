<?php

namespace Database\Seeders;

use App\Models\Course;
use Illuminate\Database\Seeder;

class CoursePackageSeeder extends Seeder
{
    public function run(): void
    {
        $silverPlus = 'Everything listed in Silver package, plus:';
        $goldPlus = 'Everything listed in Gold package, plus:';

        $catalog = [
            'pmp-certification-training' => [
                'note' => null,
                'packages' => [
                    ['Silver', 14999, 19999, null, [
                        'Instructor-led 35 Contact Hours Project Management Training',
                        'PMI Authorized PMP Exam-Prep study guide',
                        'Project Management Video Library',
                        'Project Management Templates, Flash Cards and resources',
                        'Study Plan for PMP success',
                        'WhatsApp Study Hub access',
                        'Instructor Support & Guidance until you clear PMP',
                        'Free Eligibility Assessment',
                        'Program Validity - 1 Year',
                    ]],
                    ['Gold', 19999, 24999, $silverPlus, [
                        'End to End Application Support',
                        '7 full-length mock exams (1500 unique questions)',
                        'Program Validity - 2 Years',
                    ]],
                    ['Platinum', 24999, 34999, $goldPlus, [
                        'Free Repeat Training (Until Validity Period)',
                        'Free 60-PDU Package Included for Your First PMP Renewal Cycle',
                        '25% Discount on Your Next Certification Course',
                        'Program Validity - 3 Years',
                    ]],
                ],
            ],
            'pgmp-certification-training' => [
                'note' => '* All package benefits and services are valid for one (1) year from the enrollment date or until PgMP® certification is achieved, whichever occurs earlier.',
                'packages' => [
                    ['Silver', 24999, 34999, null, [
                        'Instructor-led sessions based on the PMI Standard for Program Management (5th Edition)',
                        'In house curated Study Materials',
                        'Recorded Sessions: Your Path to PgMP Mastery',
                        'Flash Cards and practice exams',
                        'Pre-Approved Professional Development Units (PDUs) to Renew PMI Certifications',
                        'How to pass PgMP exam Podcast access',
                        'PgMP WhatsApp Hub access',
                        'Study plan to pass PgMP exam',
                    ]],
                    ['Gold', 34999, 49999, $silverPlus, [
                        'Application Review & Approval Support',
                        'PgMP Mock Exams: Prepare, Practice, and Pass with Confidence',
                        'Gap-Assessment session - (5 Sessions)',
                    ]],
                    ['Platinum', 44999, 69999, $goldPlus, [
                        'Free Repeat Training (Until Validity Period)',
                        'Gap-Assessment session - (Unlimited)',
                        'Free 60-PDU Package Included for Your First PgMP Renewal Cycle',
                        '25% Discount on Your Next Certification Course',
                    ]],
                ],
            ],
            'pfmp-certification-training' => [
                'note' => '* All package benefits and services are valid for one (1) year from the enrollment date or until PfMP® certification is achieved, whichever occurs earlier.',
                'packages' => [
                    ['Silver', 24999, 34999, null, [
                        'Instructor-led sessions aligned to Standard for Portfolio Management (4th Edition)',
                        'In house curated Study Materials',
                        'Recorded Sessions: Your Path to PfMP Mastery',
                        'Flash Cards and practice exams',
                        'PMI Pre-Approved PDUs for Certification Renewal',
                        'How to pass PfMP exam Podcast access',
                        'PfMP WhatsApp Hub access',
                        'Study plan to pass PfMP exam',
                    ]],
                    ['Gold', 34999, 49999, $silverPlus, [
                        'Application Review & Approval Support',
                        'PfMP Mock Exams: Prepare, Practice, and Pass with Confidence',
                        'Gap-Assessment session - (5 Sessions)',
                    ]],
                    ['Platinum', 44999, 69999, $goldPlus, [
                        'Free Repeat Training (Until Validity Period)',
                        'Gap-Assessment session - (Unlimited)',
                        'Free 60-PDU Package Included for Your First PfMP Renewal Cycle',
                        '25% Discount on Your Next Certification Course',
                    ]],
                ],
            ],
            'pmi-rmp-certification-training' => [
                'note' => null,
                'packages' => [
                    ['Silver', 11999, 13999, null, [
                        'Program Validity - 1 Year',
                        'Instructor-led 30 hours risk management training',
                        'PMI Authorized RMP Exam-Prep study guide',
                        'Risk Management Video Library',
                        'Instructor Support & Guidance until you clear RMP',
                        'WhatsApp Study Hub access',
                        'Study Plan for RMP success',
                        'Free Eligibility Assessment',
                    ]],
                    ['Gold', 15999, 17999, $silverPlus, [
                        'Program Validity - 2 Years',
                        'Application Review & Approval Support',
                        '3 Full-Length Mock Exams',
                    ]],
                    ['Platinum', 19999, 21999, $goldPlus, [
                        'Program Validity - 3 Years',
                        'Free Repeat Training (Until Validity Period)',
                        'Free 60-PDU Package Included for Your First PMI-RMP Renewal Cycle',
                        '25% Discount on Your Next Certification Course',
                    ]],
                ],
            ],
        ];

        foreach ($catalog as $slug => $entry) {
            $course = Course::query()->where('slug', $slug)->first();
            if (! $course) {
                continue;
            }

            if ($entry['note'] !== null) {
                $course->update(['packages_note' => $entry['note']]);
            }

            foreach ($entry['packages'] as $index => [$name, $price, $compare, $lead, $features]) {
                $course->packages()->updateOrCreate(['name' => $name], [
                    'price' => $price,
                    'compare_price' => $compare,
                    'currency' => 'INR',
                    'includes_label' => $lead,
                    'features' => implode("\n", $features),
                    'is_popular' => $name === 'Gold',
                    'sort_order' => $index + 1,
                    'status' => 'active',
                ]);
            }
        }
    }
}
