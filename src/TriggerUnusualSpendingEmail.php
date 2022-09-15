<?php

class TriggerUnusualSpendingEmail {

    private EmailSender $emailSender;
    private Clock $clock;

    public function __construct(EmailSender $emailSender, Clock $clock)
    {
        $this->emailSender = $emailSender;
        $this->clock = $clock;
    }

    public function trigger(int $userId): void
    {
        $this->emailSender->send(
            "Unusual spending of \$64.75 detected!",
            <<<EOT
Hello card user!

We have detected unusually high spending on your card in these categories:

* You spent $51.75 on Restaurants
* You spent $13.00 on Entertainment

Love,

The Credit Card Company
EOT
            );
    }
}
