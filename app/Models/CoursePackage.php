<?php

namespace App\Models;

use App\Models\Concerns\HasPublishStatus;
use App\Services\Pricing\Pricing;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CoursePackage extends Model
{
    use HasPublishStatus;

    protected $fillable = [
        'course_id', 'name', 'price', 'compare_price', 'currency', 'includes_label', 'features', 'is_popular', 'sort_order', 'status',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'compare_price' => 'decimal:2',
            'is_popular' => 'boolean',
        ];
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * @return list<string>
     */
    public function featureList(): array
    {
        return array_values(array_filter(array_map('trim', preg_split('/\R/', (string) $this->features) ?: [])));
    }

    public function discountPercent(): int
    {
        return app(Pricing::class)->percentOff($this->price, $this->compare_price);
    }

    public function money(float|string|null $amount): string
    {
        return app(Pricing::class)->format($amount);
    }
}
