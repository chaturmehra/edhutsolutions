<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuizAttempt extends Model
{
    /** The test page checks in every 20 seconds; a longer silence (closed laptop, crash) counts at most this much. */
    public const MAX_SILENCE_SECONDS = 90;

    protected $fillable = [
        'quiz_id', 'user_id', 'question_ids', 'score', 'max_score', 'percentage', 'passed',
        'started_at', 'elapsed_seconds', 'last_seen_at', 'submitted_at',
    ];

    protected function casts(): array
    {
        return [
            'question_ids' => 'array',
            'score' => 'decimal:2',
            'max_score' => 'decimal:2',
            'percentage' => 'decimal:2',
            'passed' => 'boolean',
            'started_at' => 'datetime',
            'elapsed_seconds' => 'integer',
            'last_seen_at' => 'datetime',
            'submitted_at' => 'datetime',
        ];
    }

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(QuizAttemptAnswer::class);
    }

    /**
     * Adds the time since the learner's last check-in to the active time. The clock keeps running while
     * the test page is open and stops when $present is false, which is sent as the learner leaves the page.
     */
    public function recordActivity(bool $present = true): void
    {
        $now = now();
        $elapsed = (int) $this->elapsed_seconds;

        if ($this->last_seen_at) {
            $elapsed += min(self::MAX_SILENCE_SECONDS, max(0, (int) $this->last_seen_at->diffInSeconds($now)));
        }

        $this->forceFill([
            'elapsed_seconds' => $elapsed,
            'last_seen_at' => $present ? $now : null,
        ])->save();
    }

    public function remainingSeconds(Quiz $quiz): ?int
    {
        if (! $quiz->time_limit_minutes) {
            return null;
        }

        return max(0, $quiz->time_limit_minutes * 60 - (int) $this->elapsed_seconds);
    }

    /**
     * Seconds spent on the test: active time for attempts tracked with the pause-aware clock,
     * otherwise the wall-clock time between start and submit.
     */
    public function timeSpentSeconds(): ?int
    {
        if ($this->elapsed_seconds !== null) {
            return $this->elapsed_seconds;
        }

        return $this->started_at && $this->submitted_at
            ? (int) $this->started_at->diffInSeconds($this->submitted_at)
            : null;
    }
}
