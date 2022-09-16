<?php

class TriggerUnusualSpendingEmail
{
    private EmailSender $emailSender;
    private Clock $clock;
    private PaymentRepository $paymentRepository;

    public function __construct(EmailSender $emailSender, Clock $clock, PaymentRepository $paymentRepository)
    {
        $this->emailSender = $emailSender;
        $this->clock = $clock;
        $this->paymentRepository = $paymentRepository;
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
        return [
            Category::Restaurants->name => 50.2,
            Category::Entertainment->name => 150.2,
        ];
    }
}
