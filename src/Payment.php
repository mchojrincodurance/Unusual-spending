<?php

declare(strict_types=1);

class Payment
{
    private Price $price;
    private Category $category;

    /**
     * @param Price $price
     * @param PaymentDescription $description
     * @param Category $category
     */
    public function __construct(Price $price, PaymentDescription $description, Category $category)
    {
        $this->price = $price;
        $this->category = $category;
    }

    public function getValue(): float
    {
        return $this->price->getValue();
    }

    public function getCategory(): Category
    {
        return $this->category;
    }
}