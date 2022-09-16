<?php

class EmailSender
{
    private function send(string $subject, string $body): void
    {
        // TODO: implement email sending functionality
    }

    public function sendReport(float $totalUnusualSpend, array $detailedUnusualSpending): void
    {
        $this->send($this->buildEmailSubject($totalUnusualSpend), $this->buildEmailBody($detailedUnusualSpending));
    }

    /**
     * @param array $unusualSpending
     * @return string
     */
    public function buildEmailBody(array $unusualSpending): string
    {
        return "Hello card user!

We have detected unusually high spending on your card in these categories:" . $this->buildUnusualSpendingReport($unusualSpending) . "

Love,

The Credit Card Company";
    }

    private function buildUnusualSpendingReport(array $unusualSpending): string
    {
        return implode(PHP_EOL,
            array_map(
                fn(string $category, float $spend) => "* You spent \$".number_format($spend, 2)." on $category", array_keys($unusualSpending), array_values($unusualSpending)
            )
        );
    }

    /**
     * @param float $totalUnusualSpending
     * @return string
     */
    public function buildEmailSubject(float $totalUnusualSpending): string
    {
        return "Unusual spending of \$" . number_format($totalUnusualSpending, 2) . " detected!";
    }
}