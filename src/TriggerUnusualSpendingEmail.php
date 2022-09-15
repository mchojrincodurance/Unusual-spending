<?php

class TriggerUnusualSpendingEmail {

    private EmailSender $emailSender;

    public function __construct(EmailSender $emailSender)
    {
        $this->emailSender = $emailSender;
    }

    public function trigger(int $userId): void
    {
        $this->emailSender->send();
    }
}
