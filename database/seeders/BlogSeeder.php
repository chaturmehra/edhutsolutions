<?php

namespace Database\Seeders;

use App\Models\Blog;
use DOMDocument;
use DOMXPath;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class BlogSeeder extends Seeder
{
    public function run(): void
    {
        $directory = public_path('assets/images/blogs');
        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        foreach ($this->sources() as $source) {
            $html = @file_get_contents($source);
            if ($html === false) {
                continue;
            }

            $article = $this->parse($html);
            if ($article === null) {
                continue;
            }

            $image = $this->storeImage($article['image_url'], $article['slug'], $directory);
            $blog = Blog::query()->updateOrCreate(['slug' => $article['slug']], [
                'title' => $article['title'],
                'category' => $article['category'],
                'excerpt' => $article['excerpt'],
                'content' => $article['content'],
                'image' => $image,
                'image_alt' => $article['title'],
                'published_on' => $article['published_on'],
                'status' => 'active',
            ]);

            $blog->seo()->updateOrCreate([], [
                'meta_title' => $article['title'].' | EdHut Solutions',
                'meta_keywords' => $article['keywords'],
                'meta_description' => $article['excerpt'],
            ]);
        }
    }

    /**
     * @return list<string>
     */
    private function sources(): array
    {
        return [
            'https://www.edhutsolutions.com/blog-details.php?slug=pfmp-eligibility-requirements-are-you-qualified&id=15',
            'https://www.edhutsolutions.com/blog-details.php?slug=who-should-pursue-pgmp-certification-a-strategic-career-guide&id=14',
            'https://www.edhutsolutions.com/blog-details.php?slug=what-is-pmp-certification-and-why-should-you-get-it&id=13',
        ];
    }

    /**
     * @return array{title: string, slug: string, category: string, excerpt: string, content: string, image_url: string, published_on: string, keywords: string}|null
     */
    private function parse(string $html): ?array
    {
        $document = new DOMDocument;
        libxml_use_internal_errors(true);
        $document->loadHTML('<?xml encoding="utf-8">'.$html);
        libxml_clear_errors();
        $xpath = new DOMXPath($document);

        $title = trim($xpath->evaluate('string(//h1[contains(@class,"page-header__title")])'));
        $column = $xpath->query("//div[contains(@class,'col-xl-8')]")->item(0);
        if ($title === '' || ! $column) {
            return null;
        }

        $content = '';
        foreach ($column->childNodes as $child) {
            $content .= $document->saveHTML($child);
        }
        $content = $this->clean($content);
        $text = trim(preg_replace('/\s+/', ' ', strip_tags($content)) ?? '');
        $excerpt = Str::limit($text, 180, '');
        $category = trim($xpath->evaluate('string(//section[contains(@class,"page-header")]//div[contains(@class,"text-purple-1")])'));
        $date = trim($xpath->evaluate('string(//p[contains(@class,"page-header__text")])'));
        $image = trim($xpath->evaluate('string(//div[contains(@class,"js-lazy")]/@data-bg)'));

        return [
            'title' => html_entity_decode($title, ENT_QUOTES | ENT_HTML5, 'UTF-8'),
            'slug' => Str::slug($title),
            'category' => $this->category($category),
            'excerpt' => $excerpt,
            'content' => $content,
            'image_url' => $image,
            'published_on' => $this->date($date),
            'keywords' => collect([$category, 'PMI', 'EdHut'])->filter()->unique()->implode(', '),
        ];
    }

    private function category(string $value): string
    {
        $map = [
            'PMP' => 'Project Management',
            'PgMP' => 'Program Management',
            'PfMP' => 'Portfolio Management',
            'PMI-RMP' => 'Risk Management',
            'RMP' => 'Risk Management',
        ];

        return $map[$value] ?? (array_key_exists($value, Blog::CATEGORIES) ? $value : 'Project Management');
    }

    private function clean(string $html): string
    {
        $html = preg_replace('/\?x.{0,3}(?=[A-Z&])/u', '', $html) ?? $html;
        $html = preg_replace_callback('/<p>((?:\?S\?\s*[^<]+<br>\s*)+\?S\?\s*[^<]+)<\/p>/u', function (array $match) {
            preg_match_all('/\?S\?\s*([^<]+)/u', $match[1], $items);
            $items = array_map(fn ($item) => '<li>'.trim($item).'</li>', $items[1]);

            return '<ul>'.implode('', $items).'</ul>';
        }, $html) ?? $html;
        $html = str_replace('?S?', '', $html);
        $html = preg_replace_callback('/href="https?:\/\/(?:www\.)?edhutsolutions\.com\/([^"]*)"/i', function (array $match) {
            $path = trim($match[1], '/');
            $local = [
                'pmp-certification-training' => '/courses/pmp-certification-training',
                'pgmp-certification-training' => '/courses/pgmp-certification-training',
                'pfmp-certification-training' => '/courses/pfmp-certification-training',
                'pmi-rmp-certification-training' => '/courses/pmi-rmp-certification-training',
                'blogs' => '/blogs',
                'contact-us' => '/contact',
                'about-us' => '/about-us',
            ];

            if (isset($local[$path])) {
                return 'href="'.$local[$path].'"';
            }
            if (str_starts_with($path, 'services/')) {
                return 'href="/'.$path.'"';
            }

            return $match[0];
        }, $html) ?? $html;

        return trim($html);
    }

    private function date(string $value): string
    {
        try {
            return Carbon::parse($value)->toDateString();
        } catch (\Throwable) {
            return '2025-07-10';
        }
    }

    private function storeImage(string $url, string $slug, string $directory): ?string
    {
        if ($url === '') {
            return null;
        }

        $extension = pathinfo(parse_url($url, PHP_URL_PATH) ?? '', PATHINFO_EXTENSION) ?: 'webp';
        $relative = 'assets/images/blogs/'.$slug.'.'.$extension;
        $binary = @file_get_contents($url);
        if ($binary === false) {
            return null;
        }

        file_put_contents($directory.DIRECTORY_SEPARATOR.$slug.'.'.$extension, $binary);

        return $relative;
    }
}
