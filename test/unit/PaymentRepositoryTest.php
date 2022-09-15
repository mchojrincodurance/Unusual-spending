<?php

declare(strict_types=1);


use PHPUnit\Framework\TestCase;

class PaymentRepositoryTest extends TestCase
{
    /**
     * @test
     * @dataProvider paymentsProvider
     */
    public function store_payments(array $payments): void
    {
        $currentMonth = 0;
        $userId = new UserId(0);

        $paymentRepository = new PaymentRepository(new Clock());

        foreach ($payments as $payment) {
            $paymentRepository->addPayment($userId, $payment);
        }

        $this->assertEquals($payments, $paymentRepository->getUserMonthlyPayments($userId, $currentMonth));
    }

    /**
     * @throws NegativePriceException
     */
    public function paymentsProvider() : array
    {
        return [
            [ [new Payment(new Price(10.2), new PaymentDescription('A dinner'), Category::Restaurants ), new Payment(new Price(50.6), new PaymentDescription('A bike ride'), Category::Entertainment )] ]
        ];
    }
}
