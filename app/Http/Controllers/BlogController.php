<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Support\SeoView;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BlogController extends Controller
{
    public function index(Request $request): View
    {
        $selected = $request->string('category')->toString();
        if (! array_key_exists($selected, Blog::CATEGORIES)) {
            $selected = '';
        }

        $totals = Blog::query()
            ->active()
            ->selectRaw('category, COUNT(*) as total')
            ->groupBy('category')
            ->pluck('total', 'category');
        $categories = collect(Blog::CATEGORIES)
            ->map(fn (string $label) => (object) ['category' => $label, 'total' => (int) ($totals[$label] ?? 0)])
            ->filter(fn (object $row) => $row->total > 0)
            ->values();

        $posts = Blog::query()
            ->active()
            ->when($selected !== '', fn ($query) => $query->where('category', $selected))
            ->orderByDesc('published_on')
            ->orderBy('id')
            ->paginate(3)
            ->withQueryString();

        return view('public.blogs.index', [
            'categories' => $categories,
            'selected' => $selected,
            'posts' => $posts,
            'title' => 'Blogs | '.config('app.name'),
            'description' => 'Articles from EdHut on PMP, PgMP, and PfMP preparation.',
        ]);
    }

    public function show(Blog $blog): View
    {
        abort_unless($blog->isActive(), 404);
        $blog->load('seo');

        return view('public.blogs.show', [
            'blog' => $blog,
            'related' => Blog::query()->active()->whereKeyNot($blog->id)->orderByDesc('published_on')->orderBy('id')->limit(3)->get(),
            ...SeoView::for($blog, $blog->title.' | '.config('app.name'), $blog->excerpt),
        ]);
    }
}
