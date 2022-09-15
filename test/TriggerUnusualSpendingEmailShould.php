<?php

declare(strict_types=1);

use Mockery\Adapter\Phpunit\MockeryTestCase;

class TriggerUnusualSpendingEmailShould extends MockeryTestCase
{
    /**
     * @test
     */
    public function send_emails(): void
    {
        $clock = new Clock(new DateTimeImmutable());

        $paymentRepository = new PaymentRepository($clock);
        $userId = new UserId(1);

        $paymentRepository
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
            )
        ;

        $clock->nextMonth();

        $paymentRepository
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
            )
        ;

        $emailSender = Mockery::mock('EmailSender');
        $emailSender->shouldReceive('send')
            ->once();

        $triggerUnusualSpendingEmail = new TriggerUnusualSpendingEmail();
        $triggerUnusualSpendingEmail->trigger(1);
    }
}
