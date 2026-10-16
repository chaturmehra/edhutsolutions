<?php

namespace App\Policies;

use App\Models\Course;
use App\Models\User;

class CoursePolicy
{
    public function viewContent(User $user, Course $course): bool
    {
        if (! $user->isActiveAccount() || ! $course->isActive()) {
            return false;
        }

        return $user->enrollments()
            ->where('course_id', $course->id)
            ->where('status', 'active')
            ->exists();
    }
}
