<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

class UnusualSpendingCalculatorShould extends TestCase
{
    private PaymentRepository $paymentRepository;
    private UserId $userId;

    /**
     * @test
     */
    public function return_every_spend_above_threshold(): void
    {
        $unusualSpendingCalculator = new UnusualSpendingCalculator();
        $this->assertEquals(
            [
                Category::Restaurants->name => 25,
                Category::Entertainment->name => 150,
            ],
            $unusualSpendingCalculator->getUnusualSpending(1, 1, 0));
    }

    /**
     * @throws NegativePriceException
     */
    protected function setUp(): void
    {
        $this->userId = new UserId(1);
        $this->paymentRepository = Mockery::mock(PaymentRepository::class);
        $this
            ->paymentRepository
            ->shouldReceive('getUserMonthlyPayments')
            ->once()
            ->withArgs([
                $this->userId,
                0
            ])
            ->andReturn([
                new Payment(
                    new Price(10),
                    new PaymentDescription("A nice dinner"),
                    Category::Restaurants
                ),
                new Payment(
                    new Price(50),
                    new PaymentDescription("A horse back ride"),
                    Category::Entertainment
                ),
                new Payment(
                    new Price(30),
                    new PaymentDescription("A Golf class"),
                    Category::Golf
                ),
            ])
            ->shouldReceive('getUserMonthlyPayments')
            ->once()
            ->withArgs([
                $this->userId,
                1
            ])
            ->andReturn([
                new Payment(
                    new Price(25),
                    new PaymentDescription("A very nice dinner"),
                    Category::Restaurants
                ),
                new Payment(
                    new Price(150),
                    new PaymentDescription("Swimming with dolphins"),
                    Category::Entertainment
                ),
                new Payment(
                    new Price(30),
                    new PaymentDescription("A Golf class"),
                    Category::Golf
                ),
            ])
        ;
    }
}
