<?php

declare(strict_types=1);

class Clock
{
    private int $currentMonth;

    public function __construct()
    {
        $this->currentMonth = 0;
    }

    public function nextMonth(): void
    {
        $this->currentMonth++;
    }

    public function getCurrentMonth(): int
    {
        return $this->currentMonth;
    }

    public function getPreviousMonth(): int
    {
        return $this->currentMonth -1;
    }
}