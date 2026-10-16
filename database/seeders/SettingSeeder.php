<?php

namespace Database\Seeders;

use App\Models\Setting;
use App\Services\Settings\SiteSettings;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = app(SiteSettings::class);

        foreach ($settings->defaults() as $key => $value) {
            Setting::query()->updateOrCreate(
                ['key' => $key],
                ['group' => 'general', 'value' => $value]
            );
        }

        $settings->forget();
    }
}
