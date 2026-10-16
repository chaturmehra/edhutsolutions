<?php

namespace App\Models;

use App\Models\Concerns\HasPublishStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Page extends Model
{
    use HasPublishStatus, SoftDeletes;

    protected $fillable = ['title', 'slug', 'excerpt', 'content', 'status'];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function seo(): MorphOne
    {
        return $this->morphOne(SeoMeta::class, 'seoable');
    }
}
