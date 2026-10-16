<?php

namespace App\Services\Orders;

use App\Models\Enrollment;
use App\Models\Order;
use App\Models\ServiceEnrollment;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderApproval
{
    public function approve(Order $order, User $admin, ?string $note = null): void
    {
        if ($order->status !== 'pending') {
            throw ValidationException::withMessages([
                'order' => 'Only a pending order can be approved.',
            ]);
        }

        DB::transaction(function () use ($order, $admin, $note) {
            $order->update([
                'status' => 'approved',
                'approved_by' => $admin->id,
                'approved_at' => now(),
                'rejected_at' => null,
                'admin_note' => $note,
            ]);

            foreach ($order->items()->with('servicePlan')->get() as $item) {
                if ($item->servicePlan) {
                    ServiceEnrollment::query()->updateOrCreate(
                        ['user_id' => $order->user_id, 'service_id' => $item->servicePlan->service_id],
                        [
                            'service_plan_id' => $item->servicePlan->id,
                            'order_id' => $order->id,
                            'status' => 'active',
                            'enrolled_at' => now(),
                        ],
                    );
                }

                if (! $item->course_id) {
                    continue;
                }

                Enrollment::query()->updateOrCreate(
                    ['user_id' => $order->user_id, 'course_id' => $item->course_id],
                    [
                        'order_id' => $order->id,
                        'status' => 'active',
                        'enrolled_at' => now(),
                    ],
                );
            }
        });
    }

    public function reject(Order $order, User $admin, ?string $note = null): void
    {
        if ($order->status !== 'pending') {
            throw ValidationException::withMessages([
                'order' => 'Only a pending order can be rejected.',
            ]);
        }

        $order->update([
            'status' => 'rejected',
            'approved_by' => $admin->id,
            'rejected_at' => now(),
            'admin_note' => $note,
        ]);
    }
}
