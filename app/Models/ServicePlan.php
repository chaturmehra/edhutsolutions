<?php

namespace App\Models;

use App\Models\Concerns\HasPublishStatus;
use App\Services\Pricing\Pricing;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServicePlan extends Model
{
    use HasPublishStatus;

    protected $fillable = [
        'service_id', 'label', 'price', 'compare_price', 'currency', 'description', 'sort_order', 'status',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'compare_price' => 'decimal:2',
        ];
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function priceLabel(): string
    {
        return app(Pricing::class)->label($this->price);
    }

    public function money(mixed $amount): string
    {
        return app(Pricing::class)->format($amount);
    }

    public function hasDiscount(): bool
    {
        return $this->compare_price !== null && (float) $this->compare_price > (float) $this->price;
    }

    public function discountPercent(): int
    {
        return $this->hasDiscount() ? app(Pricing::class)->percentOff($this->price, $this->compare_price) : 0;
    }
}
