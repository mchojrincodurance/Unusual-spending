<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

class UnusualSpendingCalculatorShould extends TestCase
{
    private PaymentRepository $paymentRepository;
    private UserId $userId;

    /**
     * @param array $firstMonthSpend
     * @param array $secondMonthSpend
     * @param array $expectedResult
     * @test
     * @throws NegativePriceException
     * @dataProvider spendProvider
     */
    public function return_every_spend_above_threshold(array $firstMonthSpend, array $secondMonthSpend, array $expectedResult): void
    {
        $this->buildInitialScenario($firstMonthSpend, $secondMonthSpend);

        $unusualSpendingCalculator = new UnusualSpendingCalculator();
        $this->assertEquals(
            $expectedResult,
            $unusualSpendingCalculator->getUnusualSpending(1, 1, 0));
    }

    /**
     * @param array $firstMonthSpend
     * @param array $secondMonthSpend
     * @return void
     * @throws NegativePriceException
     * @dataProvider spendProvider
     */
    private function buildInitialScenario(array $firstMonthSpend, array $secondMonthSpend): void
    {
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
                    new Price($firstMonthSpend[Category::Restaurants->name]),
                    new PaymentDescription("A nice dinner"),
                    Category::Restaurants
                ),
                new Payment(
                    new Price($firstMonthSpend[Category::Entertainment->name]),
                    new PaymentDescription("A horse back ride"),
                    Category::Entertainment
                ),
                new Payment(
                    new Price($firstMonthSpend[Category::Golf->name]),
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
                    new Price($secondMonthSpend[Category::Restaurants->name]),
                    new PaymentDescription("A very nice dinner"),
                    Category::Restaurants
                ),
                new Payment(
                    new Price($secondMonthSpend[Category::Entertainment->name]),
                    new PaymentDescription("Swimming with dolphins"),
                    Category::Entertainment
                ),
                new Payment(
                    new Price($secondMonthSpend[Category::Golf->name]),
                    new PaymentDescription("A Golf class"),
                    Category::Golf
                ),
            ]);
    }

    /**
     */
    protected function setUp(): void
    {
        $this->userId = new UserId(1);
        $this->paymentRepository = Mockery::mock(PaymentRepository::class);
    }

    private function spendProvider(): array
    {
        return
            [
                [
                    [
                        Category::Restaurants->name => 10,
                        Category::Entertainment->name => 20,
                        Category::Golf->name => 30,
                    ],
                    [
                        Category::Restaurants->name => 30,
                        Category::Entertainment->name => 50,
                        Category::Golf->name => 30,
                    ],
                    [
                        Category::Restaurants->name => 30,
                        Category::Entertainment->name => 50,
                    ]
                ]
            ];
    }
}
