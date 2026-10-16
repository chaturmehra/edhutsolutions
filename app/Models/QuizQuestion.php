<?php

namespace App\Models;

use App\Models\Concerns\HasPublishStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuizQuestion extends Model
{
    use HasPublishStatus;

    protected $fillable = [
        'quiz_id', 'type', 'prompt', 'scenario', 'marks', 'negative_marks', 'explanation', 'sort_order', 'status',
    ];

    protected function casts(): array
    {
        return [
            'marks' => 'decimal:2',
            'negative_marks' => 'decimal:2',
        ];
    }

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    public function options(): HasMany
    {
        return $this->hasMany(QuizOption::class)->orderBy('sort_order');
    }
}
