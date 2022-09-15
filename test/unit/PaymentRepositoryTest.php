<?php

declare(strict_types=1);


use PHPUnit\Framework\TestCase;

class PaymentRepositoryTest extends TestCase
{
    /**
     * @test
     */
    public function store_payments(): void
    {
        $currentMonth = 0;
        $userId = new UserId(0);

        $paymentRepository = new PaymentRepository(new Clock());

        $firstPayment = new Payment(
            new Price(1.5),
            new PaymentDescription("A payment"),
            Category::Restaurants
        );

        $monthlyPayments = [$firstPayment,];

        $paymentRepository
            ->addPayment($userId, $firstPayment);

        $this->assertEquals($monthlyPayments, $paymentRepository->getUserMonthlyPayments($userId, $currentMonth));
    }
}
