<?php

namespace App\View\Composers;

use Illuminate\View\View;

class AccountComposer
{
    public function compose(View $view): void
    {
        $user = auth()->user();
        $continue = null;
        $continueService = null;
        $waiting = null;

        if ($user) {
            $continue = $user->enrollments()->where('status', 'active')->with('course')->latest('enrolled_at')->first();
            if (! $continue) {
                $continueService = $user->serviceEnrollments()
                    ->where('status', 'active')
                    ->whereHas('service', fn ($query) => $query->active())
                    ->with('service')
                    ->latest('enrolled_at')
                    ->first();
            }
            if (! $continue && ! $continueService) {
                $waiting = $user->orders()->where('status', 'pending')->latest()->first();
            }
        }

        $view->with([
            'continueEnrollment' => $continue,
            'continueService' => $continueService,
            'waitingOrder' => $waiting,
        ]);
    }
}
