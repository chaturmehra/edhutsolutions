<?php

namespace App\Models;

use App\Models\Concerns\HasPublishStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Blog extends Model
{
    use HasPublishStatus, SoftDeletes;

    public const CATEGORIES = [
        'Project Management' => 'Project Management',
        'Program Management' => 'Program Management',
        'Portfolio Management' => 'Portfolio Management',
        'Risk Management' => 'Risk Management',
    ];

    protected $fillable = [
        'title', 'slug', 'category', 'excerpt', 'content', 'image', 'image_alt',
        'published_on', 'status',
    ];

    protected function casts(): array
    {
        return [
            'published_on' => 'date',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function seo(): MorphOne
    {
        return $this->morphOne(SeoMeta::class, 'seoable');
    }
}
