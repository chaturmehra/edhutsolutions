<?php

namespace App\Models;

use App\Models\Concerns\HasPublishStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lesson extends Model
{
    use HasPublishStatus, SoftDeletes;

    protected $fillable = [
        'course_section_id', 'title', 'slug', 'summary', 'sort_order', 'status',
    ];

    public function section(): BelongsTo
    {
        return $this->belongsTo(CourseSection::class, 'course_section_id');
    }

    public function pdfs(): HasMany
    {
        return $this->hasMany(CoursePdf::class);
    }

    public function videos(): HasMany
    {
        return $this->hasMany(CourseVideo::class);
    }

    public function quizzes(): HasMany
    {
        return $this->hasMany(Quiz::class);
    }
}
