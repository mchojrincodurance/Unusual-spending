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
        $unusualSpending = $this->getUnusualSpending($userId);
        $this->sendEmail(
            $this->buildEmailSubject(array_sum($unusualSpending)),
            $this->buildEmailBody($unusualSpending)
        );
    }

    private function sendEmail(string $subject, string $body): void
    {
        $this->emailSender->send(
            $subject,
            $body
        );
    }

    /**
     * @param float $totalUnusualSpending
     * @return string
     */
    public function buildEmailSubject(float $totalUnusualSpending): string
    {
        return "Unusual spending of \$".number_format($totalUnusualSpending, 2)." detected!";
    }

    /**
     * @param array $unusualSpending
     * @return string
     */
    public function buildEmailBody(array $unusualSpending): string
    {
        return "
Hello card user!

We have detected unusually high spending on your card in these categories:".$this->buildUnusualSpendingReport($unusualSpending)."

Love,

The Credit Card Company";
    }

    private function buildUnusualSpendingReport(array $unusualSpending): string
    {
        return implode(PHP_EOL,
            array_map(
                fn(string $category, float $spend) => "* You spent \$".number_format($spend, 2)." on $category", array_keys($unusualSpending), array_values($unusualSpending)
            )
        );
    }

    private function getUnusualSpending(int $userId): array
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
}
