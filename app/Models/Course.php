<?php

namespace App\Models;

use App\Models\Concerns\HasPublishStatus;
use App\Services\Pricing\Pricing;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
    use HasPublishStatus, SoftDeletes;

    protected $fillable = [
        'category_id', 'title', 'slug', 'short_description', 'description', 'overview', 'learning_objectives',
        'thumbnail', 'thumbnail_alt', 'price', 'currency', 'price_on_request',
        'duration', 'level', 'language', 'is_featured', 'packages_note', 'status',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'price_on_request' => 'boolean',
            'is_featured' => 'boolean',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function sections(): HasMany
    {
        return $this->hasMany(CourseSection::class)->orderBy('sort_order');
    }

    public function lessons(): HasManyThrough
    {
        return $this->hasManyThrough(Lesson::class, CourseSection::class);
    }

    public function pdfs(): HasMany
    {
        return $this->hasMany(CoursePdf::class)->orderBy('sort_order');
    }

    public function resources(): HasMany
    {
        return $this->hasMany(CourseResource::class)->orderBy('sort_order')->orderBy('id');
    }

    /**
     * @return array<int, string>
     */
    public function overviewParagraphs(): array
    {
        return array_values(array_filter(array_map('trim', preg_split('/\R\s*\R/', (string) $this->overview))));
    }

    /**
     * @return array<int, string>
     */
    public function objectiveList(): array
    {
        return array_values(array_filter(array_map('trim', preg_split('/\R/', (string) $this->learning_objectives))));
    }

    public function videos(): HasMany
    {
        return $this->hasMany(CourseVideo::class)->orderBy('sort_order');
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(CourseSchedule::class)->orderBy('starts_on');
    }

    public function faqs(): HasMany
    {
        return $this->hasMany(Faq::class)->orderBy('sort_order');
    }

    public function quizzes(): HasMany
    {
        return $this->hasMany(Quiz::class);
    }

    public function trainers(): BelongsToMany
    {
        return $this->belongsToMany(Trainer::class)->orderBy('trainers.sort_order')->orderBy('trainers.id');
    }

    public function testimonials(): HasMany
    {
        return $this->hasMany(Testimonial::class)->orderBy('sort_order');
    }

    public function alumni(): HasMany
    {
        return $this->hasMany(Alumnus::class)->orderBy('sort_order')->orderBy('id');
    }

    public function packages(): HasMany
    {
        return $this->hasMany(CoursePackage::class)->orderBy('sort_order')->orderBy('id');
    }

    public function entryPackage(): ?CoursePackage
    {
        return $this->packages->where('status', 'active')->sortBy(fn (CoursePackage $package) => (float) $package->price)->first();
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function seo(): MorphOne
    {
        return $this->morphOne(SeoMeta::class, 'seoable');
    }

    public function menuLine(): string
    {
        return match ($this->slug) {
            'pmp-certification-training' => 'For experienced project managers.',
            'pgmp-certification-training' => 'For leaders who coordinate related projects.',
            'pfmp-certification-training' => 'Align projects and programs with strategy.',
            'pmi-rmp-certification-training' => 'Identify and respond to project risk.',
            default => (string) $this->short_description,
        };
    }

    public function shortLabel(): string
    {
        return match ($this->slug) {
            'pmp-certification-training' => 'PMP',
            'pgmp-certification-training' => 'PgMP',
            'pfmp-certification-training' => 'PfMP',
            'pmi-rmp-certification-training' => 'PMI-RMP',
            default => $this->title,
        };
    }

    public function priceLabel(): string
    {
        if ($this->price_on_request || (float) $this->price <= 0) {
            return 'Request quotation';
        }

        return app(Pricing::class)->label($this->price);
    }
}
