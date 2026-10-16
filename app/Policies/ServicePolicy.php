<?php

namespace App\Policies;

use App\Models\Service;
use App\Models\User;

class ServicePolicy
{
    public function viewContent(User $user, Service $service): bool
    {
        if (! $user->isActiveAccount() || ! $service->isActive()) {
            return false;
        }

        return $user->serviceEnrollments()
            ->where('service_id', $service->id)
            ->where('status', 'active')
            ->exists();
    }
}
