<?php

declare(strict_types=1);

class Payment
{

    /**
     * @param Price $price
     * @param PaymentDescription $description
     * @param Category $category
     */
    public function __construct(Price $price, PaymentDescription $description, Category $category)
    {
    }

    /**
     * @throws NegativePriceException
     */
    public function getPrice(): Price
    {
        return new Price(0);
    }
}