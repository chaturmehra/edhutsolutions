<?php

namespace App\Services\Settings;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class SiteSettings
{
    public function all(): array
    {
        $stored = Cache::remember('site.settings', 600, function () {
            return Setting::query()->pluck('value', 'key')->all();
        });

        return array_merge($this->defaults(), $stored);
    }

    public function get(string $key, ?string $default = null): string
    {
        $all = $this->all();

        return (string) ($all[$key] ?? $default ?? '');
    }

    public function forget(): void
    {
        Cache::forget('site.settings');
    }

    /**
     * @return array<string, string>
     */
    public function defaults(): array
    {
        return [
            'site_name' => 'EdHut Solutions',
            'tagline' => 'PMI-Authorized Training Partners',
            'iso_line' => 'An ISO 9001:2015 Certified Company',
            'email' => 'info@edhutsolutions.com',
            'phone' => '+91 7042121784',
            'phone_display' => '+91 704 2121 784',
            'whatsapp' => '917042121784',
            'address' => 'F-4, G/F, Khajoori Road, Jogabai Extension, Okhla, New Delhi',
            'website' => 'www.edhutsolutions.com',
            'logo' => 'assets/images/brand/logo.png',
            'footer_text' => 'EdHut Solutions is a PMI Authorized Training Partner delivering PMI-approved instruction online and in person.',
            'facebook' => 'https://www.facebook.com/EDHUTSOLUTIONS',
            'instagram' => 'https://www.instagram.com/edhutsolution/',
            'linkedin' => 'https://www.linkedin.com/company/edhut-knowledge-solutions-pvt-ltd/',
            'youtube' => 'https://www.youtube.com/@edhutglobal',
            'x' => 'https://x.com/info_edhut',
            'rating' => '4.9',
            'review_count' => '633+',
            'stat_students' => '25,000+',
            'stat_pmp' => '6,000+',
            'stat_pgmp' => '1,000+',
            'stat_pfmp' => '425+',
            'stat_reviews' => '590+',
            'stat_corporate' => '100+',
            'notice_title' => 'Important Notice',
            'notice_body' => 'Beware of fake calls, messages, and emails. Verify all communications only through official contacts.',
        ];
    }
}
