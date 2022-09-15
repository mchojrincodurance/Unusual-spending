<?php

declare(strict_types=1);

use Mockery\Adapter\Phpunit\MockeryTestCase;

class TriggerUnusualSpendingEmailShould extends MockeryTestCase
{
    private Clock $clock;
    private PaymentRepository $paymentRepository;

    /**
     * @test
     */
    public function send_emails(): void
    {
        $this->buildBaseScenario();

        $emailSender = Mockery::spy(EmailSender::class);
        $triggerUnusualSpendingEmail = new TriggerUnusualSpendingEmail($emailSender);
        $triggerUnusualSpendingEmail->trigger(1);

        $emailSender
            ->shouldHaveReceived('send')
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
                    new Price(8.20),
                    new PaymentDescription("Bike ride"),
                    Category::Entertainment
                )
            );
    }

    protected function setUp() : void
    {
        parent::setUp();
        $this->clock = new Clock(new DateTimeImmutable());
        $this->paymentRepository = new PaymentRepository($this->clock);
    }
}
