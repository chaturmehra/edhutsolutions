<?php

namespace App\Models;

use App\Enums\UserRole;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    public const GENDERS = [
        'male' => 'Male',
        'female' => 'Female',
        'other' => 'Other',
        'prefer_not_to_say' => 'Prefer not to say',
    ];

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'gender',
        'city',
        'country_code',
        'status',
        'google_id',
        'avatar',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
        ];
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_code', 'code');
    }

    public function learnerCode(): string
    {
        return 'EH-'.str_pad((string) $this->id, 5, '0', STR_PAD_LEFT);
    }

    public function initial(): string
    {
        $letter = mb_substr(trim($this->name), 0, 1);

        return strtoupper($letter !== '' ? $letter : 'E');
    }

    public function avatarUrl(): ?string
    {
        if (! $this->avatar) {
            return null;
        }

        if (str_starts_with($this->avatar, 'http://') || str_starts_with($this->avatar, 'https://')) {
            return $this->avatar;
        }

        return is_file(public_path($this->avatar)) ? asset($this->avatar) : null;
    }

    public function isActiveAccount(): bool
    {
        return $this->status === 'active';
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === UserRole::SuperAdmin;
    }

    public function isSeoManager(): bool
    {
        return $this->role === UserRole::SeoManager;
    }

    public function cart(): HasOne
    {
        return $this->hasOne(Cart::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function serviceEnrollments(): HasMany
    {
        return $this->hasMany(ServiceEnrollment::class);
    }

    public function quizAttempts(): HasMany
    {
        return $this->hasMany(QuizAttempt::class);
    }

    public function pendingOrderFor(Course $course): ?Order
    {
        return $this->orders()
            ->where('status', 'pending')
            ->whereHas('items', fn ($items) => $items->where('course_id', $course->id))
            ->latest()
            ->first();
    }

    /**
     * Course ids the learner already owns or is waiting on.
     *
     * @return array{enrolled: list<int>, pending: list<int>}
     */
    public function purchasedCourseIds(): array
    {
        $enrolled = $this->enrollments()->where('status', 'active')->pluck('course_id')->map(fn ($id) => (int) $id)->all();
        $pending = OrderItem::query()
            ->whereNotNull('course_id')
            ->whereHas('order', fn ($order) => $order->where('user_id', $this->id)->where('status', 'pending'))
            ->pluck('course_id')
            ->map(fn ($id) => (int) $id)
            ->diff($enrolled)
            ->values()
            ->all();

        return ['enrolled' => $enrolled, 'pending' => $pending];
    }

    /**
     * Course packages the learner owns or is waiting on, as "course:package" keys. "course:*" covers
     * every package of that course, for an enrollment that did not come from a package order.
     *
     * @return array{enrolled: list<string>, pending: array<string, int>} pending maps each key to its order id
     */
    public function purchasedPackageKeys(): array
    {
        $active = $this->enrollments()->where('status', 'active')->pluck('course_id')->map(fn ($id) => (int) $id)->all();
        $items = OrderItem::query()
            ->whereNotNull('course_id')
            ->whereHas('order', fn ($order) => $order->where('user_id', $this->id)->whereIn('status', ['approved', 'pending']))
            ->with('order:id,status')
            ->get(['id', 'order_id', 'course_id', 'course_package_id']);
        $key = fn (OrderItem $item) => $item->course_id.':'.($item->course_package_id ?: '*');

        $enrolled = $items
            ->filter(fn (OrderItem $item) => $item->order->status === 'approved' && in_array((int) $item->course_id, $active, true))
            ->map($key)
            ->unique()
            ->values();
        foreach ($active as $courseId) {
            if (! $enrolled->contains(fn (string $owned) => str_starts_with($owned, $courseId.':'))) {
                $enrolled->push($courseId.':*');
            }
        }

        $pending = $items
            ->filter(fn (OrderItem $item) => $item->order->status === 'pending')
            ->reject(fn (OrderItem $item) => self::ownsPackage($enrolled->all(), (int) $item->course_id, $item->course_package_id))
            ->mapWithKeys(fn (OrderItem $item) => [$key($item) => (int) $item->order_id]);

        return ['enrolled' => $enrolled->all(), 'pending' => $pending->all()];
    }

    /**
     * @param  list<string>  $keys  keys from purchasedPackageKeys()
     */
    public static function ownsPackage(array $keys, int $courseId, int|string|null $packageId): bool
    {
        return in_array($courseId.':*', $keys, true)
            || (filled($packageId) && in_array($courseId.':'.$packageId, $keys, true));
    }

    /**
     * Service plans the learner owns or is waiting on.
     *
     * @return array{enrolled: list<int>, pending: array<int, int>} pending maps each plan id to its order id
     */
    public function purchasedPlanIds(): array
    {
        $activeServices = $this->serviceEnrollments()->where('status', 'active')->get(['service_id', 'service_plan_id']);
        $serviceIds = $activeServices->pluck('service_id')->map(fn ($id) => (int) $id)->all();
        $items = OrderItem::query()
            ->whereNotNull('service_plan_id')
            ->whereHas('order', fn ($order) => $order->where('user_id', $this->id)->whereIn('status', ['approved', 'pending']))
            ->with(['order:id,status', 'servicePlan:id,service_id'])
            ->get(['id', 'order_id', 'service_plan_id']);

        $enrolled = $items
            ->filter(fn (OrderItem $item) => $item->order->status === 'approved' && in_array((int) $item->servicePlan?->service_id, $serviceIds, true))
            ->pluck('service_plan_id')
            ->merge($activeServices->pluck('service_plan_id')->filter())
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        $pending = $items
            ->filter(fn (OrderItem $item) => $item->order->status === 'pending' && ! $enrolled->contains((int) $item->service_plan_id))
            ->mapWithKeys(fn (OrderItem $item) => [(int) $item->service_plan_id => (int) $item->order_id]);

        return ['enrolled' => $enrolled->all(), 'pending' => $pending->all()];
    }
}
