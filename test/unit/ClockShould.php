<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

class ClockShould extends TestCase
{
    /**
     * @test
     */
    public function not_skip_months(): void
    {
        $clock = new Clock();
        $previousMonth = $clock->getCurrentMonth();
        $clock->nextMonth();
        $this->assertEquals($previousMonth + 1, $clock->getCurrentMonth());
    }
}
