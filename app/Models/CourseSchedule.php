<?php

namespace App\Models;

use App\Models\Concerns\HasPublishStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class CourseSchedule extends Model
{
    use HasPublishStatus;

    public const DEFAULT_TRAINER = 'PMI Authorized Trainer';

    protected $fillable = [
        'course_id', 'starts_on', 'ends_on', 'time_label', 'hours_label', 'delivery_mode', 'session_dates', 'trainer_id', 'status',
    ];

    protected function casts(): array
    {
        return [
            'starts_on' => 'date',
            'ends_on' => 'date',
            'session_dates' => 'array',
        ];
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function trainer(): BelongsTo
    {
        return $this->belongsTo(Trainer::class);
    }

    public function trainerName(): string
    {
        return $this->trainer?->status === 'active' ? $this->trainer->name : self::DEFAULT_TRAINER;
    }

    /**
     * Cohorts that have not finished yet: still listed and open for enrollment until the last day,
     * or the start day when no end date is set.
     */
    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->where(function (Builder $query) {
            $query->whereDate('ends_on', '>=', today())
                ->orWhere(fn (Builder $query) => $query->whereNull('ends_on')->whereDate('starts_on', '>=', today()));
        });
    }

    public function hasStarted(): bool
    {
        return $this->starts_on !== null && $this->starts_on->lte(today());
    }

    public function rangeLabel(): string
    {
        if (! $this->starts_on) {
            return 'Date to be announced';
        }
        if (! $this->ends_on || $this->ends_on->isSameDay($this->starts_on)) {
            return $this->starts_on->format('d M Y');
        }

        $start = $this->starts_on->format($this->starts_on->isSameYear($this->ends_on) ? 'd M' : 'd M Y');

        return $start.' - '.$this->ends_on->format('d M Y');
    }

    public function scheduleSummary(): string
    {
        $days = $this->sessionDays();
        if ($days->isNotEmpty() && $days->every(fn (Carbon $day) => $day->isWeekend())) {
            $weekends = $days->map(fn (Carbon $day) => $day->copy()->startOfWeek()->toDateString())->unique()->count();

            return $weekends.' '.str('Weekend')->plural($weekends);
        }

        return $days->count().' '.str('Session')->plural($days->count());
    }

    /**
     * @return Collection<int, Carbon>
     */
    public function sessionDays(): Collection
    {
        return collect($this->session_dates ?? [])
            ->filter()
            ->map(fn ($date) => Carbon::parse($date))
            ->sort()
            ->values();
    }
}
