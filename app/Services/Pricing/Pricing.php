<?php

namespace App\Services\Pricing;

/**
 * Turns catalog prices, which are stored in INR, into what the current visitor sees and pays.
 */
class Pricing
{
    public const HOME_CURRENCY = 'INR';

    public function __construct(private VisitorCountry $visitor) {}

    public function country(): string
    {
        return $this->visitor->code();
    }

    public function isForeign(): bool
    {
        if (request()->is('admin', 'admin/*')) {
            return false;
        }

        return $this->country() !== config('edhut.pricing.home_country', 'IN');
    }

    public function currency(): string
    {
        return $this->isForeign() ? (string) config('edhut.pricing.foreign_currency', 'USD') : self::HOME_CURRENCY;
    }

    public function amount(float|string|null $inr): float
    {
        $inr = (float) $inr;
        if ($inr <= 0 || ! $this->isForeign()) {
            return $inr;
        }

        return round($inr * (float) config('edhut.pricing.inr_to_foreign_rate') + (float) config('edhut.pricing.foreign_markup'), 2);
    }

    /**
     * The converted amount without a currency, e.g. "14,999" or "162.49".
     */
    public function number(float|string|null $inr): string
    {
        return self::digits($this->amount($inr), $this->currency());
    }

    /**
     * e.g. "INR 14,999" or "USD 162.49".
     */
    public function format(float|string|null $inr): string
    {
        return $this->currency().' '.$this->number($inr);
    }

    /**
     * An amount that amount() already converted, such as a cart total.
     */
    public function formatConverted(float $amount): string
    {
        return $this->currency().' '.self::digits($amount, $this->currency());
    }

    /**
     * e.g. "₹14,999" or "USD 162.49".
     */
    public function label(float|string|null $inr): string
    {
        return self::display($this->amount($inr), $this->currency());
    }

    public function percentOff(float|string|null $price, float|string|null $compare): int
    {
        $price = $this->amount($price);
        $compare = $this->amount($compare);

        return $compare > $price && $compare > 0 ? (int) round(($compare - $price) / $compare * 100) : 0;
    }

    /**
     * An amount that is already in $currency, such as an order total.
     */
    public static function display(float $amount, string $currency): string
    {
        return $currency === self::HOME_CURRENCY
            ? '₹'.self::digits($amount, $currency)
            : $currency.' '.self::digits($amount, $currency);
    }

    private static function digits(float $amount, string $currency): string
    {
        $whole = $currency === self::HOME_CURRENCY && floor($amount) === $amount;

        return number_format($amount, $whole ? 0 : 2);
    }
}
