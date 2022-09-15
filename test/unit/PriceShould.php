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
        $this->doesNotPerformAssertions();
        $price = new Price(1);
    }
}
