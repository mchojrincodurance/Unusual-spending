<?php

declare(strict_types=1);

class PaymentRepository
{
    private array $payments;
    private Clock $clock;

    public function __construct(Clock $clock)
    {
        $this->payments = [];
        $this->clock = $clock;
    }

    public function addPayment(UserId $userId, Payment $payment): self
    {
        $this->payments[$this->clock->getCurrentMonth()][$userId->getValue()][] = $payment;
        
        return $this;
    }

    public function getUserMonthlyPayments(UserId $userId, int $month): array
    {
        return array_key_exists($month, $this->payments) ? 
            array_key_exists($userId->getValue(), $this->payments[$month]) ? $this->payments[$month][$userId->getValue()] : [] 
            : [];
    }
}