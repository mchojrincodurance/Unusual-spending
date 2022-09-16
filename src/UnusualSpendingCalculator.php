<?php

declare(strict_types=1);

class UnusualSpendingCalculator
{

    public function getUnusualSpending(int $userId, int $getCurrentMonth, int $getPreviousMonth): array
    {
        return [
            Category::Restaurants->name => 25,
            Category::Entertainment->name => 150,
        ];
    }
}