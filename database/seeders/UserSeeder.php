<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->updateOrCreate(['email' => 'admin@edhut.test'], [
            'name' => 'EdHut Admin',
            'password' => 'EdhutAdmin#2026',
            'role' => UserRole::SuperAdmin,
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        User::query()->updateOrCreate(['email' => 'seo@edhut.test'], [
            'name' => 'EdHut SEO',
            'password' => 'EdhutSeo#2026',
            'role' => UserRole::SeoManager,
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        User::query()->updateOrCreate(['email' => 'demo@edhut.test'], [
            'name' => 'Demo Learner',
            'password' => 'EdhutDemo#2026',
            'role' => UserRole::Student,
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        User::query()->updateOrCreate(['email' => 'student@edhut.test'], [
            'name' => 'Asha Learner',
            'password' => 'EdhutUser#2026',
            'role' => UserRole::Student,
            'status' => 'active',
            'email_verified_at' => now(),
        ]);
    }
}
