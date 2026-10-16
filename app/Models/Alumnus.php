<?php

namespace App\Models;

use App\Models\Concerns\HasPublishStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Alumnus extends Model
{
    use HasPublishStatus, SoftDeletes;

    protected $table = 'alumni';

    protected $fillable = ['course_id', 'name', 'image', 'job_title', 'sort_order', 'status'];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function initials(): string
    {
        $parts = collect(preg_split('/\s+/', trim($this->name)))->filter()->values();

        return strtoupper(mb_substr((string) $parts->first(), 0, 1).mb_substr((string) $parts->last(), 0, 1));
    }
}
