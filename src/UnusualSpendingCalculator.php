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
        $currentMonthPayments = $this->getUserMonthlyPayments($userId, $currentMonth);
        $previousMonthPayments = $this->getUserMonthlyPayments($userId, $previousMonth);

        return $this->buildUnusualSpending($currentMonthPayments, $previousMonthPayments);
    }

    private function getUserMonthlyPayments(UserId $userId, int $month): array
    {
        return $this->paymentRepository->getUserMonthlyPayments($userId, $month);
    }

    private function buildUnusualSpending(array $currentMonthPayments, array $previousMonthPayments): array
    {
        $currentTotalSpend = $this->getTotalSpend($currentMonthPayments);
        $previousTotalSpend = $this->getTotalSpend($previousMonthPayments);

        $unusualSpending = [];

        foreach ($currentTotalSpend as $category => $spend) {
            if ($this->isUnusual($previousTotalSpend[$category], $spend)) {
                $unusualSpending[$category] = $spend;
            }
        }

        return $unusualSpending;
    }

    /**
     * @param array $currentMonthPayments
     * @return array
     */
    public function getTotalSpend(array $currentMonthPayments): array
    {
        $currentTotalSpend = [];

        foreach (Category::cases() as $category) {
            $currentTotalSpend[$category->name] = array_sum(
                array_map(
                    fn(Payment $payment) => $payment->getValue(),
                    array_filter(
                        $currentMonthPayments,
                        fn(Payment $payment) => $payment->getCategory() === $category
                    )
                )
            );
        }

        return $currentTotalSpend;
    }

    /**
     * @param $previousTotalSpend
     * @param mixed $spend
     * @return bool
     */
    public function isUnusual($previousTotalSpend, mixed $spend): bool
    {
        return $spend >= $previousTotalSpend * $this->unusualSpendMultiplier;
    }
}