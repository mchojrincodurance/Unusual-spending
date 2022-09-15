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
     * @dataProvider valueProvider
     */
    public function store_its_value(float $value): void
    {
        $price = new Price($value);

        $this->assertEquals($value, $price->getValue());
    }

    public function valueProvider(): array
    {
        return [
            [ 10 ],
            [ 20 ],
            [ 30.9 ],
        ];
    }
}
