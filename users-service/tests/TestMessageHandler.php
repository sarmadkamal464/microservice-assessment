<?php
// tests/TestMessageHandler.php

namespace App\Tests;

use App\Message\UserCreatedEvent;

class TestMessageHandler
{
    private $messages = [];

    public function __invoke(UserCreatedEvent $message)
    {
        $this->messages[] = $message;
    }

    public function getMessages(): array
    {
        return $this->messages;
    }
}