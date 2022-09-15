<?php

declare(strict_types=1);

class PaymentRepository
{

    public function __construct(Clock $clock)
    {
    }

    public function addPayment(UserId $userId, Payment $payment): self
    {
        return $this;
    }

    public function getUserMonthlyPayments(UserId $userId, int $month): array
    {
        return [];
    }
}