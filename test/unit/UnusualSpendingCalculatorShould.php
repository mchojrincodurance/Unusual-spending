<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

class UnusualSpendingCalculatorShould extends TestCase
{
    private PaymentRepository $paymentRepository;
    private UserId $userId;
    private UnusualSpendingCalculator $unusualSpendingCalculator;

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

        $this->assertEquals(
            $expectedResult,
            $this->unusualSpendingCalculator->getUnusualSpending($this->userId, 1, 0));
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
        $paymentsMade = [];

        foreach ($firstMonthSpend as $category => $spend) {
            $paymentsMade[] = new Payment(
                new Price($spend),
                new PaymentDescription("A payment"),
                Category::from($category)
            );
        }

        $this
            ->paymentRepository
            ->shouldReceive('getUserMonthlyPayments')
            ->once()
            ->withArgs([
                $this->userId,
                0
            ])
            ->andReturn($paymentsMade)
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
        $this->unusualSpendingCalculator = new UnusualSpendingCalculator(
            $this->paymentRepository,
            2
        );
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
                ],
                [
                    [
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
