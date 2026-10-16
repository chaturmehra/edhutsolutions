<?php

namespace App\Models;

use App\Models\Concerns\HasPublishStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Testimonial extends Model
{
    use HasPublishStatus, SoftDeletes;

    protected $fillable = [
        'course_id', 'name', 'designation', 'headline', 'quote', 'rating', 'avatar', 'sort_order', 'status',
    ];

    protected function casts(): array
    {
        return ['rating' => 'decimal:1'];
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
}
