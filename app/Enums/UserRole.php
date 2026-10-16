<?php

namespace App\Enums;

enum UserRole: string
{
    case SuperAdmin = 'super_admin';
    case SeoManager = 'seo_manager';
    case Student = 'student';

    public function label(): string
    {
        return match ($this) {
            self::SuperAdmin => 'Super Admin',
            self::SeoManager => 'SEO Manager',
            self::Student => 'Student',
        };
    }

    public function homePath(): string
    {
        return match ($this) {
            self::SuperAdmin => '/admin',
            self::SeoManager => '/admin/seo',
            self::Student => '/account',
        };
    }
}
