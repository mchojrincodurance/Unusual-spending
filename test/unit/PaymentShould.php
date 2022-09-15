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
        $value = 10.0;
        $price = new Price($value);
        $payment = new Payment($price, new PaymentDescription("Description"), Category::Entertainment);
        $this->assertSame($value, $payment->getValue());
    }
}
