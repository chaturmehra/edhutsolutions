<?php

namespace App\Models;

use App\Models\Concerns\HasPublishStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Faq extends Model
{
    use HasPublishStatus;

    protected $fillable = ['course_id', 'question', 'answer', 'sort_order', 'status'];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
}
