<?php

namespace App\Contracts;

use App\Models\Order;

interface PaymentService
{
    public function charge(Order $order): PaymentResult;
}
