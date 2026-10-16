<?php

namespace App\Models;

use App\Models\Concerns\HasPublishStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourseResource extends Model
{
    use HasPublishStatus, SoftDeletes;

    protected $fillable = [
        'course_id', 'title', 'description', 'file_path', 'original_name', 'file_size', 'sort_order', 'status',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function hasFile(): bool
    {
        return filled($this->file_path);
    }
}
