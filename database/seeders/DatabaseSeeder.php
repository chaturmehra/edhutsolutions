<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            SettingSeeder::class,
            CatalogSeeder::class,
            CoursePackageSeeder::class,
            CmsSeeder::class,
            BlogSeeder::class,
            ClientSeeder::class,
        ]);
    }
}
