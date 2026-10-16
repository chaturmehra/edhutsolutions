<?php

namespace App\Services\Payments;

use App\Contracts\PaymentResult;
use App\Contracts\PaymentService;
use App\Models\Order;

class ManualPaymentService implements PaymentService
{
    public function charge(Order $order): PaymentResult
    {
        return new PaymentResult(
            status: 'not_required',
            reference: 'manual-'.$order->number,
            message: 'No payment gateway is connected. The order stays pending until an administrator approves it.',
        );
    }
}
