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
        $lastMonthSpend = $this->calculateMonthlySpend($userId, $this->getCurrentMonth() - 1);
        $currentMonthSpend = $this->calculateMonthlySpend($userId, $this->getCurrentMonth());

        $unusualSpending = [];

        foreach ($currentMonthSpend as $category => $spend) {
            if (array_key_exists($category, $lastMonthSpend) && $lastMonthSpend[$category] < $spend / 2) {
                $unusualSpending[$category] = $spend;
            }
        }

        $this->sendUnusualSpendingEmail($unusualSpending);
    }

    private function getCategories(): array
    {
        return Category::cases();
    }

    private function getCategoryMonthlySpendForUser(Category $category, int $userId, int $month): float
    {
        $categoryPayments = array_filter(
            $this->getUserMonthlyPayments($userId, $month),
            fn(Payment $payment) => $payment->getCategory() === $category
        );

        return array_sum(array_map(fn(Payment $payment) => $payment->getValue(), $categoryPayments));
    }

    private function getCurrentMonth(): int
    {
        return $this->getClock()->getCurrentMonth();
    }

    private function getClock(): Clock
    {
        return $this->clock;
    }

    private function getUserMonthlyPayments(int $userId, int $month): array
    {
        return $this
            ->paymentRepository
            ->getUserMonthlyPayments(new UserId($userId), $month);
    }

    /**
     * @param int $userId
     * @param int $month
     * @return array
     */
    public function calculateMonthlySpend(int $userId, int $month): array
    {
        $monthlySpend = [];

        foreach ($this->getCategories() as $category) {
            $monthlySpend[$category->name] = $this->getCategoryMonthlySpendForUser($category, $userId, $month);
        }

        return $monthlySpend;
    }

    /**
     * @param array $unusualSpending
     * @return void
     */
    public function sendUnusualSpendingEmail(array $unusualSpending): void
    {
        $totalUnusualSpending = array_sum($unusualSpending);

        $body = "Hello card user!

We have detected unusually high spending on your card in these categories:

";

        foreach ($unusualSpending as $category => $spend) {
            $body .= "* You spent \$" . number_format($spend, 2) . " on $category" . PHP_EOL;
        }

        $body .= "Love,

The Credit Card Company
        ";

        $this->emailSender->send(
            "Unusual spending of \$$totalUnusualSpending detected!",
            $body
        );
    }
}
