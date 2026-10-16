<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Support\SeoView;
use Illuminate\View\View;

class PageController extends Controller
{
    public function show(Page $page): View
    {
        abort_unless($page->isActive(), 404);
        $page->load('seo');

        if ($page->slug === 'about') {
            return view('public.pages.about', [
                'page' => $page,
                ...SeoView::for($page, 'About us | '.config('app.name'), $page->excerpt),
            ]);
        }

        return view('public.pages.show', [
            'page' => $page,
            ...SeoView::for($page, $page->title.' | '.config('app.name'), $page->excerpt),
        ]);
    }
}
