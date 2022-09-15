<?php

class TriggerUnusualSpendingEmail {

    private EmailSender $emailSender;
    private Clock $clock;

    public function __construct(EmailSender $emailSender, Clock $clock)
    {
        $this->emailSender = $emailSender;
        $this->clock = $clock;
    }

    public function trigger(int $userId): void
    {
        $currentMonthSpend = [];

        foreach ($this->getCategories() as $category) {
            $currentMonthSpend[$category] = $this->getCategoryMonthlySpendForUser($category, $userId, $this->getCurrentMonth());
        }


        $this->emailSender->send(
            "Unusual spending of \$64.75 detected!",
            <<<EOT
Hello card user!

We have detected unusually high spending on your card in these categories:

* You spent $51.75 on Restaurants
* You spent $13.00 on Entertainment

Love,

The Credit Card Company
EOT
            );
    }

    private function getCategories(): array
    {
        return Category::cases();
    }

    private function getCategoryMonthlySpendForUser(Category $category, int $userId, int $month): float
    {
        $userMonthlyPayments = $this->getUserMonthlyPayments($userId, $month);
        $categoryPayments = array_filter($userMonthlyPayments, fn(Payment $payment) => $payment->getCategory() === $category);

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

    private function getUserMonthlyPayments(int $userId, int $month) : array
    {
        return [];
    }
}
