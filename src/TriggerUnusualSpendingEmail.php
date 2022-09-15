<?php

class TriggerUnusualSpendingEmail {

    private EmailSender $emailSender;

    public function __construct(EmailSender $emailSender)
    {
        $this->emailSender = $emailSender;
    }

    public function trigger(int $userId): void
    {
        $this->emailSender->send(
            "Unusual spending of $118.20 detected!",
            <<<EOT
Hello card user!

We have detected unusually high spending on your card in these categories:

* You spent $100.00 on restaurants

Love,

The Credit Card Company
EOT
            );
    }
}
