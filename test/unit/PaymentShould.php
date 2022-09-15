<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

class PaymentShould extends TestCase
{
    /**
     * @test
     * @throws NegativePriceException
     * @dataProvider valueProvider
     */
    public function store_the_price(float $value): void
    {
        $price = new Price($value);
        $payment = new Payment($price, new PaymentDescription("Description"), Category::Entertainment);
        $this->assertSame($value, $payment->getValue());
    }

    public function valueProvider(): array
    {
        return [
            [10.0],
            [11.0],
            [29.87],
        ];
    }
}
