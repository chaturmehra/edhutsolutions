<?php

namespace App\Models;

use App\Services\Pricing\Pricing;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id', 'number', 'status', 'payment_status', 'payment_method',
        'subtotal', 'total', 'currency', 'phone', 'state', 'country_code', 'admin_note', 'approved_by',
        'approved_at', 'rejected_at',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'total' => 'decimal:2',
            'approved_at' => 'datetime',
            'rejected_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_code', 'code');
    }

    /**
     * An amount stored on this order, in the currency the learner was charged.
     */
    public function money(float|string|null $amount): string
    {
        return Pricing::display((float) $amount, $this->currency ?: Pricing::HOME_CURRENCY);
    }
}
