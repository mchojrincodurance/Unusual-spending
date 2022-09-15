<?php

declare(strict_types=1);

use Mockery\Adapter\Phpunit\MockeryTestCase;

class TriggerUnusualSpendingEmailShould extends MockeryTestCase
{
    private Clock $clock;
    private PaymentRepository $paymentRepository;
    private EmailSender $emailSenderSpy;
    private TriggerUnusualSpendingEmail $triggerUnusualSpendingEmail;

    /**
     * @test
     */
    public function sent_email_with_expected_body_and_subject(): void
    {
        $this->buildBaseScenario();

        $this->triggerUnusualSpendingEmail->trigger(1);

        $this->emailSenderSpy
            ->shouldHaveReceived('send', [
                    $this->buildExpectedSubject(118.2),
                    $this->buildExpectedBody([
                        Category::Restaurants->name => 100,
                        Category::Entertainment->name => 18.2,
                    ])
                ]
            )
            ->once();
    }

    /**
     * @return void
     */
    public function buildBaseScenario(): void
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
                    new Price(20.70),
                    new PaymentDescription("Dinner with the wife"),
                    Category::Restaurants
                )
            )
            ->addPayment(
                $userId,
                new Payment(
                    new Price(5.20),
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
                    new Price(100.00),
                    new PaymentDescription("Dinner with friends"),
                    Category::Restaurants
                )
            )
            ->addPayment(
                $userId,
                new Payment(
                    new Price(18.20),
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
