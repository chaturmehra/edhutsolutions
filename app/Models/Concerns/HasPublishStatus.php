<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;

trait HasPublishStatus
{
    public function scopeActive(Builder $query): Builder
    {
        return $query->where($this->getTable().'.status', 'active');
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}
