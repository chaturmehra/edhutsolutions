<?php

namespace App\Models;

use App\Models\Concerns\HasPublishStatus;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Quiz extends Model
{
    use HasPublishStatus, SoftDeletes;

    public const KINDS = [
        'practice' => 'Practice test',
        'mock' => 'Mock test',
    ];

    protected $fillable = [
        'course_id', 'lesson_id', 'title', 'slug', 'description', 'kind',
        'negative_marking', 'pass_percentage', 'time_limit_minutes', 'questions_per_attempt', 'status',
    ];

    protected function casts(): array
    {
        return [
            'negative_marking' => 'decimal:2',
            'pass_percentage' => 'decimal:2',
            'questions_per_attempt' => 'integer',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(QuizQuestion::class)->orderBy('sort_order');
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(QuizAttempt::class);
    }

    public function seo(): MorphOne
    {
        return $this->morphOne(SeoMeta::class, 'seoable');
    }

    public function isMock(): bool
    {
        return $this->kind === 'mock';
    }

    public function kindLabel(): string
    {
        return self::KINDS[$this->kind] ?? self::KINDS['practice'];
    }

    /**
     * How many questions one attempt shows, given how many active questions the quiz has.
     */
    public function attemptSize(int $available): int
    {
        if ($this->isMock() && $this->questions_per_attempt) {
            return min($available, $this->questions_per_attempt);
        }

        return $available;
    }

    /**
     * The active question ids for a new attempt: a fresh random draw for a mock test,
     * or null so a practice test always shows every question in the set order.
     *
     * @return array<int, int>|null
     */
    public function drawQuestionIds(): ?array
    {
        if (! $this->isMock()) {
            return null;
        }

        $ids = $this->questions()->active()->pluck('id')->shuffle();

        return $ids->take($this->attemptSize($ids->count()))->map(fn ($id) => (int) $id)->values()->all();
    }

    /**
     * The questions of an attempt in the order the learner sees them.
     *
     * @param  array<int, int>|null  $questionIds
     * @return Collection<int, QuizQuestion>
     */
    public function questionsFor(?array $questionIds): Collection
    {
        $query = $this->questions()->active()->with('options');

        if ($questionIds === null) {
            return $query->get();
        }

        $position = array_flip(array_map('intval', $questionIds));

        return $query->whereIn('id', array_keys($position))->get()
            ->sortBy(fn (QuizQuestion $question) => $position[$question->id])
            ->values();
    }
}
