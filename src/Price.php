<?php

declare(strict_types=1);

class Price
{
    /**
     * @param float $value
     * @throws NegativePriceException
     */
    public function __construct(float $value)
    {
        throw new NegativePriceException();
    }
}