<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Model;

class SeoView
{
    /**
     * @return array{title: string, description: ?string, keywords: ?string}
     */
    public static function for(Model $model, string $title, ?string $description = null): array
    {
        $seo = $model->relationLoaded('seo') ? $model->getRelation('seo') : $model->seo()->first();

        return [
            'title' => filled($seo?->meta_title) ? $seo->meta_title : $title,
            'description' => filled($seo?->meta_description) ? $seo->meta_description : $description,
            'keywords' => filled($seo?->meta_keywords) ? $seo->meta_keywords : null,
        ];
    }
}
