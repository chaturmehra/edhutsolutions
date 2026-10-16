<?php

namespace App\Models;

use App\Models\Concerns\HasPublishStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Trainer extends Model
{
    use HasPublishStatus, SoftDeletes;

    protected $fillable = [
        'name', 'image', 'credentials', 'experience', 'bio', 'linkedin_url', 'sort_order', 'status',
    ];

    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class);
    }

    public function initials(): string
    {
        $parts = collect(preg_split('/\s+/', trim($this->name)))->filter()->values();

        return strtoupper(mb_substr((string) $parts->first(), 0, 1).mb_substr((string) $parts->last(), 0, 1));
    }
}
