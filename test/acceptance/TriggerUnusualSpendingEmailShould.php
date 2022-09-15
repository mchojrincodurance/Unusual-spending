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
    private TriggerUnusualSpendingEmail $triggerUnusualSpendingEmail;

    /**
     * @param float $restaurantSpend
     * @param float $entertainmentSpend
     * @param float $secondMonthMultiplier
     * @test
     * @dataProvider dataProvider
     */
    public function sent_email_with_expected_body_and_subject(float $restaurantSpend, float $entertainmentSpend, float $secondMonthMultiplier): void
    {
        $this->buildBaseScenario($restaurantSpend, $entertainmentSpend, $secondMonthMultiplier);

        $this->triggerUnusualSpendingEmail->trigger(1);

        $this->emailSenderSpy
            ->shouldHaveReceived('send', [
                    $this->buildExpectedSubject(($restaurantSpend + $entertainmentSpend) * $secondMonthMultiplier),
                    $this->buildExpectedBody([
                        Category::Restaurants->name => $restaurantSpend * $secondMonthMultiplier,
                        Category::Entertainment->name => $entertainmentSpend * $secondMonthMultiplier,
                    ])
                ]
            )
            ->once();
    }

    public function dataProvider(): array
    {
        return [
            [20.7, 5.2, 2.5],
            [15, 12, 3],
        ];
    }

    /**
     * @param float $restaurantSpend
     * @param float $entertainmentSpend
     * @param float $secondMonthMultiplier
     * @return void
     */
    public function buildBaseScenario(float $restaurantSpend, float $entertainmentSpend, float $secondMonthMultiplier): void
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

    protected function setUp(): void
    {
        parent::setUp();
        $this->clock = new Clock(new DateTimeImmutable());
        $this->paymentRepository = new PaymentRepository($this->clock);
        $this->emailSenderSpy = Mockery::spy(EmailSender::class);
        $this->triggerUnusualSpendingEmail = new TriggerUnusualSpendingEmail($this->emailSenderSpy);
    }

    private function buildExpectedBody(array $unusualExpenses): string
    {
        $return = <<<EOT
Hello card user!

We have detected unusually high spending on your card in these categories:


EOT;
        foreach ($unusualExpenses as $category => $spend) {
            $return .= "* You spent \$" . number_format($spend, 2) . " on $category" . PHP_EOL;
        }

        $return .= <<<EOT

Love,

The Credit Card Company
EOT;

        return $return;
    }

    private function buildExpectedSubject(float $totalUnusualSpend): string
    {
        return "Unusual spending of \$" . number_format($totalUnusualSpend, 2) . " detected!";
    }
}
