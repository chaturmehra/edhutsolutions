<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ServiceLearningController extends Controller
{
    public function index(Request $request): View
    {
        $filter = $request->string('status')->toString();
        if (! in_array($filter, ['all', 'approved', 'pending'], true)) {
            $filter = 'all';
        }

        $user = $request->user();
        $enrollments = $user->serviceEnrollments()
            ->whereHas('service')
            ->with(['service' => fn ($query) => $query->withCount([
                'pdfs' => fn ($pdfs) => $pdfs->active(),
                'videos' => fn ($videos) => $videos->active(),
            ]), 'plan'])
            ->latest('enrolled_at')
            ->get();
        $pending = $user->orders()
            ->where('status', 'pending')
            ->whereHas('items', fn ($items) => $items->whereNotNull('service_plan_id'))
            ->with(['items' => fn ($items) => $items->whereNotNull('service_plan_id')->with('servicePlan.service')])
            ->latest()
            ->get();

        return view('account.services.index', [
            'filter' => $filter,
            'enrollments' => match ($filter) {
                'pending' => collect(),
                'approved' => $enrollments->where('status', 'active')->values(),
                default => $enrollments,
            },
            'pending' => $filter === 'approved' ? collect() : $pending,
            'counts' => [
                'all' => $enrollments->count() + $pending->sum(fn ($order) => $order->items->count()),
                'approved' => $enrollments->where('status', 'active')->count(),
                'pending' => $pending->sum(fn ($order) => $order->items->count()),
            ],
        ]);
    }

    public function show(Request $request, Service $service): View
    {
        $this->authorize('viewContent', $service);

        $service->load([
            'pdfs' => fn ($query) => $query->active(),
            'videos' => fn ($query) => $query->active(),
        ]);

        return view('account.services.show', [
            'service' => $service,
            'enrollment' => $request->user()->serviceEnrollments()->where('service_id', $service->id)->with(['plan', 'order'])->first(),
        ]);
    }
}
