<?php

declare(strict_types=1);

use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * @todo Add a test to check that only categories with over spend are included in the email
 */
class TriggerUnusualSpendingEmailShould extends MockeryTestCase
{
    private Clock $clock;
    private PaymentRepository $paymentRepository;
    private EmailSender $emailSenderSpy;
    private UnusualSpendingCalculator $unusualSpendingCalculator;
    private TriggerUnusualSpendingEmail $triggerUnusualSpendingEmail;

    /**
     * @param float $restaurantSpend
     * @param float $entertainmentSpend
     * @param float $secondMonthMultiplier
     * @test
     * @dataProvider dataProvider
     * @throws NegativePriceException
     */
    public function sent_email_with_expected_body_and_subject(float $restaurantSpend, float $entertainmentSpend, float $secondMonthMultiplier): void
    {
        $this->buildBaseScenario($restaurantSpend, $entertainmentSpend, $secondMonthMultiplier);

        $this->triggerUnusualSpendingEmail->trigger(1);

        $this->emailSenderSpy
            ->shouldHaveReceived(
                'sendReport',
                [
                    ($restaurantSpend + $entertainmentSpend) * $secondMonthMultiplier,
                    [
                        Category::Restaurants->name => $restaurantSpend * $secondMonthMultiplier,
                        Category::Entertainment->name => $entertainmentSpend * $secondMonthMultiplier,
                    ]
                ]
            )
            ->once();
    }

    /**
     * @param float $restaurantSpend
     * @param float $entertainmentSpend
     * @param float $secondMonthMultiplier
     * @return void
     * @throws NegativePriceException
     */
    private function buildBaseScenario(float $restaurantSpend, float $entertainmentSpend, float $secondMonthMultiplier): void
    {
        $userId = new UserId(1);

        $this->paymentRepository
            ->addPayment(
                $userId,
                new Payment(
                    new Price(10.50),
                    new PaymentDescription("Golf class"),
                    Category::Golf
                )
            )
            ->addPayment(
                $userId,
                new Payment(
                    new Price($restaurantSpend),
                    new PaymentDescription("Dinner with the wife"),
                    Category::Restaurants
                )
            )
            ->addPayment(
                $userId,
                new Payment(
                    new Price($entertainmentSpend),
                    new PaymentDescription("Movie ticket"),
                    Category::Entertainment
                )
            );

        $this->clock->nextMonth();

        $this->paymentRepository
            ->addPayment(
                $userId,
                new Payment(
                    new Price(10.50),
                    new PaymentDescription("Golf class"),
                    Category::Golf
                )
            )
            ->addPayment(
                $userId,
                new Payment(
                    new Price($restaurantSpend * $secondMonthMultiplier),
                    new PaymentDescription("Dinner with friends"),
                    Category::Restaurants
                )
            )
            ->addPayment(
                $userId,
                new Payment(
                    new Price($entertainmentSpend * $secondMonthMultiplier),
                    new PaymentDescription("Bike ride"),
                    Category::Entertainment
                )
            );
    }

    public function dataProvider(): array
    {
        return [
            [20.7, 5.2, 2.5],
            [15, 12, 3],
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->clock = new Clock();
        $this->paymentRepository = new PaymentRepository($this->clock);
        $this->emailSenderSpy = Mockery::spy(EmailSender::class);
        $this->unusualSpendingCalculator = new UnusualSpendingCalculator($this->paymentRepository, 2);
        $this->triggerUnusualSpendingEmail = new TriggerUnusualSpendingEmail($this->emailSenderSpy, $this->clock, $this->unusualSpendingCalculator);
    }
}
