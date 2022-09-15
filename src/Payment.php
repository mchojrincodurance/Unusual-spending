<?php

declare(strict_types=1);

class Payment
{
    private Price $price;

    /**
     * @param Price $price
     * @param PaymentDescription $description
     * @param Category $category
     */
    public function __construct(Price $price, PaymentDescription $description, Category $category)
    {
        $this->price = $price;
    }

    /**
     */
    public function getPrice(): Price
    {
        return $this->price;
    }
}