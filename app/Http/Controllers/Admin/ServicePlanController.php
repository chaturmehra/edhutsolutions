<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServicePlan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ServicePlanController extends Controller
{
    public function index(int $service): View
    {
        $service = $this->service($service);
        $service->load('plans');

        return view('admin.services.plans', ['service' => $service]);
    }

    public function create(int $service): View
    {
        $service = $this->service($service);

        return view('admin.services.plan-form', [
            'service' => $service,
            'plan' => null,
            'nextOrder' => (int) $service->plans()->max('sort_order') + 1,
        ]);
    }

    public function store(Request $request, int $service): RedirectResponse
    {
        $service = $this->service($service);
        $service->plans()->create($this->data($request));

        return redirect()->route('admin.services.plans.index', $service->id)->with('status', 'Price saved.');
    }

    public function edit(int $service, int $plan): View
    {
        $service = $this->service($service);

        return view('admin.services.plan-form', [
            'service' => $service,
            'plan' => $this->plan($service, $plan),
            'nextOrder' => 1,
        ]);
    }

    public function update(Request $request, int $service, int $plan): RedirectResponse
    {
        $service = $this->service($service);
        $this->plan($service, $plan)->update($this->data($request));

        return redirect()->route('admin.services.plans.index', $service->id)->with('status', 'Price updated.');
    }

    public function destroy(int $service, int $plan): RedirectResponse
    {
        $service = $this->service($service);
        $this->plan($service, $plan)->delete();

        return redirect()->route('admin.services.plans.index', $service->id)->with('status', 'Price removed.');
    }

    private function service(int $service): Service
    {
        return Service::query()->findOrFail($service);
    }

    private function plan(Service $service, int $plan): ServicePlan
    {
        return $service->plans()->findOrFail($plan);
    }

    private function data(Request $request): array
    {
        foreach (['compare_price', 'description', 'sort_order'] as $key) {
            if ($request->input($key) === '') {
                $request->merge([$key => null]);
            }
        }

        $data = $request->validate([
            'label' => ['required', 'string', 'max:40'],
            'price' => ['required', 'numeric', 'min:0', 'max:99999999'],
            'compare_price' => ['nullable', 'numeric', 'min:0', 'max:99999999', 'gte:price'],
            'description' => ['nullable', 'string', 'max:500'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ], [
            'compare_price.gte' => 'The original price must be the same as or higher than the price.',
        ]);

        return [
            'label' => $data['label'],
            'price' => $data['price'],
            'compare_price' => $data['compare_price'] ?? null,
            'currency' => 'INR',
            'description' => $data['description'] ?? null,
            'sort_order' => $data['sort_order'] ?? 0,
            'status' => $data['status'],
        ];
    }
}
