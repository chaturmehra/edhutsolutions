<?php

namespace App\Models;

use App\Models\Concerns\HasPublishStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CoursePdf extends Model
{
    use HasPublishStatus, SoftDeletes;

    protected $fillable = [
        'course_id', 'service_id', 'lesson_id', 'title', 'description', 'file_path', 'original_name',
        'file_size', 'sort_order', 'status',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * The course or service this file belongs to; access to it follows that owner's enrollment.
     */
    public function owner(): Course|Service|null
    {
        return $this->service_id ? $this->service : $this->course;
    }

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }
}
