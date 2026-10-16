<?php

namespace App\Models;

use App\Models\Concerns\HasPublishStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
{
    use HasPublishStatus, SoftDeletes;

    protected $fillable = [
        'title', 'slug', 'short_description', 'description', 'thumbnail',
        'thumbnail_alt', 'sort_order', 'status',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function plans(): HasMany
    {
        return $this->hasMany(ServicePlan::class)->orderBy('sort_order');
    }

    public function pdfs(): HasMany
    {
        return $this->hasMany(CoursePdf::class)->orderBy('sort_order')->orderBy('id');
    }

    public function videos(): HasMany
    {
        return $this->hasMany(CourseVideo::class)->orderBy('sort_order')->orderBy('id');
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(ServiceEnrollment::class);
    }

    public function seo(): MorphOne
    {
        return $this->morphOne(SeoMeta::class, 'seoable');
    }

    public function startingPriceLabel(): string
    {
        $plan = $this->entryPlan();

        return $plan ? 'From '.$plan->priceLabel() : 'Request quotation';
    }

    public function entryPlan(): ?ServicePlan
    {
        return $this->plans->where('status', 'active')->sortBy(fn (ServicePlan $plan) => (float) $plan->price)->first();
    }
}
