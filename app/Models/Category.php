<?php

namespace App\Models;

use App\Models\Concerns\HasPublishStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasPublishStatus, SoftDeletes;

    protected $fillable = [
        'name', 'slug', 'description', 'image', 'image_alt', 'sort_order', 'status',
    ];

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
