<?php

class TriggerUnusualSpendingEmail
{
    private EmailSender $emailSender;
    private Clock $clock;
    private UnusualSpendingCalculator $unusualSpendingCalculator;

    public function __construct(EmailSender $emailSender, Clock $clock, UnusualSpendingCalculator $unusualSpendingCalculator)
    {
        $this->emailSender = $emailSender;
        $this->clock = $clock;
        $this->unusualSpendingCalculator = $unusualSpendingCalculator;
    }

    public function trigger(int $userId): void
    {
        $unusualSpending = $this->getUnusualSpending(new UserId($userId));
        $this->sendReport(array_sum($unusualSpending), $unusualSpending);
    }

    private function getUnusualSpending(UserId $userId): array
    {
        return $this
            ->unusualSpendingCalculator
            ->getUnusualSpending(
                $userId,
                $this->getCurrentMonth(),
                $this->getPreviousMonth()
            );
    }

    private function getCurrentMonth(): int
    {
        return $this->clock->getCurrentMonth();
    }

    private function getPreviousMonth(): int
    {
        return $this->clock->getPreviousMonth();
    }

    private function sendReport(float $totalUnusualSpend, array $detailedUnusualSpending): void
    {
        $this->emailSender->sendReport($totalUnusualSpend, $detailedUnusualSpending);
    }
}
