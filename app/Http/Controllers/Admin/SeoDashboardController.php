<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\Course;
use App\Models\Page;
use App\Models\Redirect;
use App\Models\Service;
use Illuminate\View\View;

class SeoDashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.seo.dashboard', [
            'missing' => [
                'courses' => Course::query()->active()->whereDoesntHave('seo')->count(),
                'services' => Service::query()->active()->whereDoesntHave('seo')->count(),
                'pages' => Page::query()->active()->whereDoesntHave('seo')->count(),
                'blogs' => Blog::query()->active()->whereDoesntHave('seo')->count(),
            ],
            'redirects' => Redirect::query()->latest()->get(),
        ]);
    }
}
