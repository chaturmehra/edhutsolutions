<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    protected $fillable = ['cart_id', 'course_id', 'service_plan_id', 'course_package_id', 'course_schedule_id', 'price'];

    protected function casts(): array
    {
        return ['price' => 'decimal:2'];
    }

    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function servicePlan(): BelongsTo
    {
        return $this->belongsTo(ServicePlan::class);
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(CoursePackage::class, 'course_package_id');
    }

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(CourseSchedule::class, 'course_schedule_id');
    }

    public function label(): string
    {
        if ($this->service_plan_id) {
            return ($this->servicePlan?->service?->title ?? 'Service').' – '.($this->servicePlan?->label ?? '');
        }

        $title = $this->course?->title ?? 'Course';
        if ($this->package) {
            $title .= ' – '.$this->package->name.' package';
        }

        return $this->schedule ? $title.' · '.$this->schedule->rangeLabel() : $title;
    }

    public function badge(): string
    {
        if ($this->service_plan_id) {
            return $this->servicePlan?->label ?? 'Service';
        }

        return $this->course?->shortLabel() ?? 'Course';
    }

    public function link(): string
    {
        if ($this->service_plan_id) {
            return $this->servicePlan?->service ? route('services.show', $this->servicePlan->service) : route('services.index');
        }

        return $this->course ? route('courses.show', $this->course) : route('courses.index');
    }
}
