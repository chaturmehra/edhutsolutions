<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\Course;
use App\Models\Page;
use App\Models\Service;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $urls = collect([
            route('home'),
            route('courses.index'),
            route('services.index'),
            route('blogs.index'),
            route('contact'),
            route('quizzes.index'),
        ])->merge(Course::query()->active()->pluck('slug')->map(fn ($slug) => route('courses.show', $slug)))
            ->merge(Service::query()->active()->pluck('slug')->map(fn ($slug) => route('services.show', $slug)))
            ->merge(Blog::query()->active()->pluck('slug')->map(fn ($slug) => route('blogs.show', $slug)))
            ->merge(Page::query()->active()->pluck('slug')->map(fn ($slug) => url('/'.$slug)));

        $body = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
        $body .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";

        foreach ($urls->unique() as $url) {
            $body .= '  <url><loc>'.e($url).'</loc></url>'."\n";
        }

        $body .= '</urlset>';

        return response($body, 200, ['Content-Type' => 'application/xml']);
    }

    public function robots(): Response
    {
        $lines = [
            'User-agent: *',
            'Disallow: /admin',
            'Disallow: /account',
            'Disallow: /checkout',
            'Disallow: /learn',
            'Sitemap: '.url('/sitemap.xml'),
        ];

        return response(implode("\n", $lines)."\n", 200, ['Content-Type' => 'text/plain']);
    }
}
