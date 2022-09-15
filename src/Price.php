<?php

declare(strict_types=1);

class Price
{
    private float $value;

    /**
     * @param float $value
     * @throws NegativePriceException
     */
    public function __construct(float $value)
    {
        if ($value < 0) {

            throw new NegativePriceException();
        }

        $this->value = $value;
    }

    public function getValue(): float
    {
        return $this->value;
    }
}