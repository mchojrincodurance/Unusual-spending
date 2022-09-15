<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

class PriceShould extends TestCase
{
    /**
     * @test
     */
    public function not_allow_negative_numbers(): void
    {
        $this->expectException(NegativePriceException::class);
        $price = new Price(-1);
    }

    /**
     * @return void
     * @test
     * @throws NegativePriceException
     */
    public function allow_non_negative_numbers(): void
    {
        $this->expectNotToPerformAssertions();
        $price = new Price(1);
    }

    /**
     * @test
     * @throws NegativePriceException
     */
    public function store_its_value(): void
    {
        $value = 20;
        $price = new Price($value);

        $this->assertEquals($value, $price->getValue());
    }
}
