<?php

namespace App\Contracts;

class PaymentResult
{
    public function __construct(
        public string $status,
        public string $reference,
        public string $message = '',
    ) {}
}
