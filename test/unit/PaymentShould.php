<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

class PaymentShould extends TestCase
{
    /**
     * @test
     * @throws NegativePriceException
     */
    public function store_the_price(): void
    {
        $price = new Price(10);
        $payment = new Payment($price, new PaymentDescription("Description"), Category::Entertainment);
        $this->assertSame($price, $payment->getPrice());
    }
}
