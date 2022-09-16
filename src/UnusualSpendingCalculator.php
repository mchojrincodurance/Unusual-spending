<?php

declare(strict_types=1);

class UnusualSpendingCalculator
{
    private PaymentRepository $paymentRepository;
    private int $unusualSpendMultiplier;

    public function __construct(PaymentRepository $paymentRepository, int $unusualSpendMultiplier)
    {
        $this->paymentRepository = $paymentRepository;
        $this->unusualSpendMultiplier = $unusualSpendMultiplier;
    }

    public function getUnusualSpending(UserId $userId, int $currentMonth, int $previousMonth): array
    {
        return $this->getUnusualSpends(
            $this->getTotalSpend($this->getUserMonthlyPayments($userId, $currentMonth)),
            $this->getTotalSpend($this->getUserMonthlyPayments($userId, $previousMonth))
        );
    }

    private function getUserMonthlyPayments(UserId $userId, int $month): array
    {
        return $this->paymentRepository->getUserMonthlyPayments($userId, $month);
    }

    /**
     * @param array $currentMonthPayments
     * @return array
     */
    private function getTotalSpend(array $currentMonthPayments): array
    {
        $currentTotalSpend = [];

        foreach (Category::cases() as $category) {
            $currentTotalSpend[$category->name] = $this->getTotalSpendByCategory($currentMonthPayments, $category);
        }

        return $currentTotalSpend;
    }

    /**
     * @param array $currentTotalSpend
     * @param array $previousTotalSpend
     * @return array
     */
    private function getUnusualSpends(array $currentTotalSpend, array $previousTotalSpend): array
    {
        return array_filter($currentTotalSpend, fn($spend, $category) => $this->isUnusual($previousTotalSpend[$category], $spend), ARRAY_FILTER_USE_BOTH );
    }

    /**
     * @param float $previousTotalSpend
     * @param float $spend
     * @return bool
     */
    private function isUnusual(float $previousTotalSpend, float $spend): bool
    {
        return $spend >= $previousTotalSpend * $this->unusualSpendMultiplier;
    }

    /**
     * @param array $currentMonthPayments
     * @param Category $category
     * @return float
     */
    public function getTotalSpendByCategory(array $currentMonthPayments, Category $category): float
    {
        return array_sum(
            array_map(
                fn(Payment $payment) => $payment->getValue(),
                array_filter(
                    $currentMonthPayments,
                    fn(Payment $payment) => $payment->getCategory() === $category
                )
            )
        );
    }
}