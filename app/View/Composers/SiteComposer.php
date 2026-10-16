<?php

namespace App\View\Composers;

use App\Models\Category;
use App\Models\Client;
use App\Models\CorporateImage;
use App\Models\Course;
use App\Models\Service;
use App\Services\Cart\CartService;
use App\Services\Pricing\Pricing;
use App\Services\Settings\SiteSettings;
use Illuminate\View\View;

class SiteComposer
{
    public function __construct(private SiteSettings $settings, private CartService $carts, private Pricing $pricing) {}

    public function compose(View $view): void
    {
        $view->with(once(function () {
            return [
                'site' => $this->settings,
                'pricing' => $this->pricing,
                'navCategories' => Category::query()->active()->with(['courses' => fn ($query) => $query->active()->orderBy('title')])->orderBy('sort_order')->orderBy('name')->get(),
                'navCourses' => Course::query()->active()->get()->sortBy(function (Course $course) {
                    $order = ['pmp-certification-training', 'pgmp-certification-training', 'pfmp-certification-training', 'pmi-rmp-certification-training'];
                    $index = array_search($course->slug, $order, true);

                    return $index === false ? 99 : $index;
                })->values(),
                'navServices' => Service::query()->active()->with(['plans' => fn ($query) => $query->active()->orderBy('sort_order')])->orderBy('sort_order')->orderBy('title')->get(),
                'clients' => Client::query()->active()->orderBy('sort_order')->orderBy('name')->get(),
                'corporateImages' => CorporateImage::query()->active()->orderBy('sort_order')->orderBy('id')->get(),
                'cartItems' => $this->carts->items(),
            ];
        }));
    }
}
