<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\Settings\SiteSettings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingController extends Controller
{
    /**
     * @var array<string, array{title: string, text: string, keys: list<string>}>
     */
    private const GROUPS = [
        'general' => [
            'title' => 'Settings',
            'text' => 'Site name, logo, and the brand lines used across the site.',
            'keys' => ['site_name', 'tagline', 'iso_line', 'website', 'logo', 'footer_text'],
        ],
        'contact' => [
            'title' => 'Contact details',
            'text' => 'Shown in the footer and on the contact page. Course enrollment and corporate enquiries are emailed to this address.',
            'keys' => ['email', 'phone', 'phone_display', 'whatsapp', 'address'],
        ],
        'social' => [
            'title' => 'Social media',
            'text' => 'Profile links for the footer icons. Leave a field empty to hide that icon.',
            'keys' => ['facebook', 'instagram', 'linkedin', 'x', 'youtube'],
        ],
        'stats' => [
            'title' => 'Stats and reviews',
            'text' => 'Numbers shown on the homepage, about page, course pages, and review badges.',
            'keys' => ['stat_students', 'stat_pmp', 'stat_pgmp', 'stat_pfmp', 'stat_corporate', 'stat_reviews', 'rating', 'review_count'],
        ],
        'notice' => [
            'title' => 'Notice popup',
            'text' => 'The notice visitors see when they open the site.',
            'keys' => ['notice_title', 'notice_body'],
        ],
    ];

    /**
     * @var array<string, string>
     */
    private const LABELS = [
        'iso_line' => 'ISO line',
        'phone_display' => 'Phone (as displayed)',
        'whatsapp' => 'WhatsApp number (digits only)',
        'linkedin' => 'LinkedIn',
        'x' => 'X (Twitter)',
        'youtube' => 'YouTube',
        'stat_students' => 'Professionals trained',
        'stat_pmp' => 'PMP certified',
        'stat_pgmp' => 'PgMP certified',
        'stat_pfmp' => 'PfMP certified',
        'stat_corporate' => 'Corporate cohorts delivered',
        'stat_reviews' => 'Google 5-star reviews',
        'review_count' => 'Total reviews (next to the rating)',
    ];

    public function edit(): View
    {
        $settings = Setting::query()->orderBy('key')->get()->keyBy('key')->toBase();
        $groups = [];

        foreach (self::GROUPS as $name => $group) {
            $groups[$name] = $group + ['settings' => $settings->only($group['keys'])->sortBy(fn ($setting) => array_search($setting->key, $group['keys'], true))];
        }

        $other = $settings->except(array_merge(...array_column(self::GROUPS, 'keys')));
        if ($other->isNotEmpty()) {
            $groups['other'] = ['title' => 'Other settings', 'text' => 'Any other values used across the site.', 'settings' => $other];
        }

        return view('admin.settings.edit', [
            'groups' => array_filter($groups, fn ($group) => $group['settings']->isNotEmpty()),
            'labels' => self::LABELS,
        ]);
    }

    public function update(Request $request, SiteSettings $siteSettings): RedirectResponse
    {
        $values = $request->validate([
            'settings' => ['required', 'array'],
            'settings.*' => ['nullable', 'string'],
        ]);

        foreach ($values['settings'] as $key => $value) {
            Setting::query()->where('key', $key)->update(['value' => $value]);
        }

        $siteSettings->forget();

        return back()->with('status', 'Settings saved.');
    }
}
