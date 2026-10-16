<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Page;
use App\Models\Service;
use App\Models\Testimonial;
use Illuminate\Database\Seeder;

class CmsSeeder extends Seeder
{
    public function run(): void
    {
        $this->services();
        $this->testimonials();
        $this->pages();
    }

    private function services(): void
    {
        $writing = Service::query()->updateOrCreate(['slug' => 'application-writing-support'], [
            'title' => 'Application Writing Support',
            'short_description' => 'Sample applications, a step-by-step writing guide, review sessions, and help rewriting a rejected PMI application.',
            'description' => '<ul><li>Sample PMI approved applications for reference</li><li>Detailed step-by-step guide on how to write a PMI certification exam application</li><li>Review and feedback sessions before you submit to PMI</li><li>Feedback and rewriting if PMI rejects the application</li></ul><p>The service page states: 100% money back guaranteed if your application gets rejected.</p><p>Homepage cards list these fees in INR. The same service page also shows some fees in US dollars, including PMP at $50 and PgMP at $199.98.</p>',
            'thumbnail' => 'assets/images/services/application-writing.webp',
            'thumbnail_alt' => 'Application writing support',
            'sort_order' => 1,
            'status' => 'active',
        ]);
        $this->plans($writing, [
            ['PMP', 2500],
            ['PgMP', 6000],
            ['PfMP', 6000],
            ['RMP', 2500],
        ]);

        $self = Service::query()->updateOrCreate(['slug' => 'self-learning-course'], [
            'title' => 'Self Learning Course',
            'short_description' => 'Learn your way, succeed your way. Course content aligned to the latest published standard, with examples and case studies.',
            'description' => '<p>Learn Your Way, Succeed Your Way.</p><ul><li>Based on the latest published standard of the certification body</li><li>Covers the course content and is aligned to the certification exam</li><li>Online interactive sessions with expert trainers</li><li>Content explained with examples and case studies</li></ul>',
            'thumbnail' => 'assets/images/services/self-learning.webp',
            'thumbnail_alt' => 'Self learning course',
            'sort_order' => 2,
            'status' => 'active',
        ]);
        $this->plans($self, [
            ['PgMP', 9999],
            ['PfMP', 9999],
            ['RMP', 9999],
        ]);

        $exam = Service::query()->updateOrCreate(['slug' => 'exam-simulation-tool'], [
            'title' => 'Exam Simulation Tool',
            'short_description' => 'Question banks across the exam domains, with answers verified by certified trainers and a justification for every option.',
            'description' => '<p>Simulate, Strengthen, Succeed.</p><ul><li>Question banks covering the domains and supporting concepts</li><li>Question patterns that familiarise you with the exam</li><li>Answers verified by PgMP and PfMP certified industry expert trainers</li><li>A justification for every right and wrong answer</li></ul>',
            'thumbnail' => 'assets/images/services/exam-simulation.webp',
            'thumbnail_alt' => 'Exam simulation tool',
            'sort_order' => 3,
            'status' => 'active',
        ]);
        $this->plans($exam, [
            ['PMP', 6999],
            ['PgMP', 9999],
            ['PfMP', 11999],
        ]);

        $pdu = Service::query()->updateOrCreate(['slug' => 'pdu-course'], [
            'title' => 'PDU Course',
            'short_description' => 'A PMI-approved 60-hour PDU course with three months of self-paced learning and help claiming PDUs.',
            'description' => '<ul><li>PMI-approved 60-hour PDU course</li><li>Self-paced learning module for three months</li><li>Query discussions by email with certified instructors</li><li>Support to claim PDUs and maintain CCR</li></ul><p>The published description says the program is for busy professionals who want flexibility, with instructors available by email and assistance claiming PDUs for Continuing Certification Requirements.</p>',
            'thumbnail' => 'assets/images/services/pdu.webp',
            'thumbnail_alt' => 'PDU course',
            'sort_order' => 4,
            'status' => 'active',
        ]);
        $this->plans($pdu, [
            ['PMP', 6999],
            ['RMP', 6999],
            ['PDU', 4999],
        ]);

        $advisory = Service::query()->updateOrCreate(['slug' => 'certification-advisory'], [
            'title' => 'Certification Advisory',
            'short_description' => 'A 30-minute one-to-one consultation to assess eligibility and recommend a PMI certification path.',
            'description' => '<p>At EdHut, the right certification is treated as a step in career advancement. The advisory process starts with a focused 30-minute one-on-one consultation. Advisors assess background, experience, and career objectives, then recommend the PMI certification that fits the current role and future goals.</p><p>The service page lists INR 1500, with INR 2550 shown as the earlier figure.</p>',
            'thumbnail' => null,
            'thumbnail_alt' => 'Certification advisory',
            'sort_order' => 5,
            'status' => 'active',
        ]);
        $this->plans($advisory, [
            ['One to One', 1500, 2550],
        ]);

        Service::query()->updateOrCreate(['slug' => 'corporate-training'], [
            'title' => 'Corporate Training',
            'short_description' => 'Custom PMI-aligned training for teams, delivered online or in person by experienced trainers.',
            'description' => '<p>As a PMI Authorized Training Partner, EdHut offers training that follows PMI standards. Trainers are experienced professionals. Programs are customised to an organisation’s needs, goals, and industry, and can be delivered in person or online.</p><ul><li>Custom programs aligned to company objectives</li><li>Curriculum updated to current methods</li><li>In-person and online formats</li><li>Simulations, workshops, and practical case studies</li><li>Study materials and ongoing professional development support</li></ul>',
            'thumbnail' => 'assets/images/gallery/training-2.png',
            'thumbnail_alt' => 'Corporate training session',
            'sort_order' => 6,
            'status' => 'active',
        ]);
    }

    private function plans(Service $service, array $plans): void
    {
        if ($service->plans()->count() > 0) {
            return;
        }

        foreach ($plans as $index => $plan) {
            $service->plans()->create([
                'label' => $plan[0],
                'price' => $plan[1],
                'compare_price' => $plan[2] ?? null,
                'currency' => 'INR',
                'sort_order' => $index + 1,
                'status' => 'active',
            ]);
        }
    }

    private function testimonials(): void
    {
        $quotes = [
            ['Soni Kumari', 'Excellent Training Session', 'I want to start by expressing my gratitude for the excellent training session. The course material was well-structured, and the trainer’s expertise was evident in every module. Complex topics were explained with real-life examples, interactive discussions, and case studies.', null],
            ['Bhuvan Sharma', 'Exceptional vILT PMP', 'I attended EdHut face-to-face classroom training before the COVID-19 pandemic, and later their online instructor-led program. EdHut has consistently upheld its standards. Mr. Asjad’s teaching style, combined with his experience, keeps participants engaged with real-life scenarios.', 'pmp-certification-training'],
            ['Sankaranand P Ganesan', 'Professional Trainers for PgMP', 'I had a great experience with Mr. Asjad Usmani during PgMP training. He has a unique way of teaching, and he made the subject simple and relevant with real-life examples. I highly recommend him for the courses he offers.', 'pgmp-certification-training'],
            ['Vipin Rajak', 'Well Structured and Informative Session', 'I attended PMP certification training with Asjad. He explained complex concepts clearly, encouraged questions, and made sure every topic was understood.', 'pmp-certification-training'],
            ['Philip John', 'Truly professional', 'Very engaging and interactive sessions for the PMP certification. Mr. Asjad drives focus to the key details. The training was planned and delivered so it could be understood.', 'pmp-certification-training'],
            ['Vivek Shukla', 'Insightful PMP session', 'I appreciate Mr. Asjad for an insightful and engaging PMP training session. He clarified doubts and explained complex topics in a simplified manner. I feel more confident in my PMP preparation.', 'pmp-certification-training'],
            ['kandarp joshi', 'Detailed PMP guidance', 'Mr. Asjad Usmani explained each point in detail with appropriate examples. He has very sound knowledge of PMP. I was happy to be part of the training.', 'pmp-certification-training'],
            ['aiyappa n', 'Clear and dedicated sessions', 'I am grateful to Asjad for delivering every session with clarity and dedication. The guidance made complex concepts easier to understand.', null],
        ];

        $courses = Course::query()->pluck('id', 'slug');

        foreach ($quotes as $index => $quote) {
            Testimonial::query()->updateOrCreate(['name' => $quote[0], 'headline' => $quote[1]], [
                'course_id' => $quote[3] ? $courses[$quote[3]] ?? null : null,
                'quote' => $quote[2],
                'rating' => 5,
                'sort_order' => $index + 1,
                'status' => 'active',
            ]);
        }
    }

    private function pages(): void
    {
        Page::query()->updateOrCreate(['slug' => 'about'], [
            'title' => 'About Us',
            'excerpt' => 'EdHut Solutions is a PMI Authorized Training Partner based in Okhla, New Delhi.',
            'content' => '<p>The public /about-us address on the current site did not return a page, so this introduction is assembled only from copy published on the homepage, the corporate training page, and the logo.</p><p>EdHut Solutions is a PMI Authorized Training Partner and an ISO 9001:2015 certified company. PMI Authorized Training Partners deliver high-quality, PMI-approved instruction to help professionals advance their skills and prepare for PMI certifications, online and in person.</p><p>Visit the studio at F-4, G/F, Khajoori Road, Jogabai Extension, Okhla, New Delhi, write to info@edhutsolutions.com, or call +91 704 2121 784.</p><h2>Published results</h2><ul><li>25,000+ students worldwide</li><li>6,000+ PMP certified alumni</li><li>1,000+ PgMP certified alumni</li><li>425+ PfMP certified alumni</li><li>590+ Google 5-star reviews</li><li>100+ corporate cohorts</li></ul><h2>Why existing clients choose EdHut</h2><ul><li>Globally recognised training that meets PMI standards</li><li>Trainers with industry knowledge and PMI certification expertise</li><li>Training shaped to an organisation’s needs, goals, and industry</li><li>In-person and online formats</li><li>Practical examples and case studies</li></ul>',
            'status' => 'active',
        ]);

        Page::query()->updateOrCreate(['slug' => 'become-an-instructor'], [
            'title' => 'Become an Instructor',
            'excerpt' => 'EdHut looks for qualified instructors who want to teach with industry veterans.',
            'content' => '<p>Do you have a passion for teaching and expertise to share? EdHut looks for qualified instructors. Joining as a trainer is a collaborative journey with industry veterans, designed to present you and your content clearly.</p><p>As an instructor, you build skills beyond the classroom, gain visibility through the platform, and earn income while helping learners reach professional milestones. EdHut provides training tools, a community of instructors, and course creation and technical support.</p><h2>Apply</h2><p>If you are ready to guide learners, use the contact form. After the application is reviewed, a team member reaches out if there is a match.</p><ul><li>Collaborate with industry experts</li><li>Expand your impact and visibility</li><li>Access premium training resources</li><li>Earn while inspiring others</li></ul>',
            'status' => 'active',
        ]);

        Page::query()->updateOrCreate(['slug' => 'privacy-policy'], [
            'title' => 'Privacy Policy',
            'excerpt' => 'How EdHut collects, uses, and protects information submitted on this website.',
            'content' => '<h2>What information do we collect?</h2><p>We collect information when you register, place an order, or fill out a form. You may be asked for your name, email address, mailing address, and phone number.</p><h2>What do we use your information for?</h2><ul><li>To improve the website from the feedback we receive</li><li>To respond more effectively to customer service requests</li><li>To process transactions. Information is not sold, exchanged, or transferred to another company without consent, other than to deliver the product or service requested</li><li>To send order updates and occasional company news</li></ul><h2>How do we protect your information?</h2><p>We use security measures when you place an order or submit personal information. The published policy describes a secure server and SSL for sensitive payment information.</p><h2>Do we use cookies?</h2><p>Yes. Cookies help the site remember a browser and compile aggregate data about traffic and interaction.</p><h2>Do we disclose information to outside parties?</h2><p>We do not sell, trade, or otherwise transfer personally identifiable information to outside parties. This does not include trusted parties who help operate the website, provided they keep the information confidential. Information may also be released when required to comply with the law or protect rights, property, or safety.</p><h2>Your consent</h2><p>By using the site, you consent to this privacy policy. This online policy applies to information collected through the website. If the policy changes, the update will be posted on this page.</p>',
            'status' => 'active',
        ]);

        Page::query()->updateOrCreate(['slug' => 'refund-cancellation-transfer-policy'], [
            'title' => 'Refund, Cancellation & Transfer Policy',
            'excerpt' => 'Classroom and online cancellation rules published for EdHut training.',
            'content' => '<p>When you buy a training course on the EdHut website, you agree to the privacy policy, terms of use, and the points below.</p><h2>Classroom training</h2><ul><li>EdHut may postpone or cancel an event, or change its location, because of insufficient enrolments, instructor illness, or force majeure.</li><li>If EdHut cancels an event, a 100% refund is paid to the delegate.</li><li>If a delegate cancels 7 days or more before the event, 15% of the paid fee is deducted and the rest is refunded.</li><li>If a delegate cancels 7 days or less before the event, no refund is made.</li></ul><h2>Online training</h2><ul><li>If a delegate cancels within 48 hours of subscribing, 10% is deducted as an administration fee.</li><li>If a delegate cancels after 48 hours of subscribing, no refund is made.</li></ul><p>All refunds are processed within 30 days of the refund request.</p>',
            'status' => 'active',
        ]);

        Page::query()->updateOrCreate(['slug' => 'terms'], [
            'title' => 'Terms of Use',
            'excerpt' => 'How published EdHut website content may be used.',
            'content' => '<h2>Use of content</h2><p>Website content that can be seen, heard, or otherwise experienced on the site is copyrighted and belongs to EdHut. You may use the site, the service, and the content for your own personal, non-commercial use only.</p>',
            'status' => 'active',
        ]);

        Page::query()->updateOrCreate(['slug' => 'disclaimer'], [
            'title' => 'Disclaimer',
            'excerpt' => 'Official EdHut communication is made only through the published email and mobile number.',
            'content' => '<h2>Important fraud alert</h2><p>EdHut has been informed that some unauthorized individuals make fake calls, messages, and emails using the name of EdHut Solutions. All official communication is made only through these contacts:</p><ul><li>Email: info@edhutsolutions.com</li><li>Mobile: +91 704 2121 784</li></ul><p>Students, clients, and partners should verify any suspicious call, message, payment request, or email before responding or paying. EdHut Solutions is not responsible for loss caused by fraudulent activity conducted by unauthorized persons using the company name. For verification, use the official website or the official contact details.</p>',
            'status' => 'active',
        ]);
    }
}
