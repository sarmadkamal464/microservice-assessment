<?php

namespace App\Tests\Unit;

use App\Message\UserCreatedEvent;
use PHPUnit\Framework\TestCase;

class UserCreatedTest extends TestCase
{
    public function testUserCreatedMessage(): void
    {
        $message = new UserCreatedEvent(1, 'test@example.com', 'John', 'Doe');

        $this->assertEquals(1, $message->getId());
        $this->assertEquals('test@example.com', $message->getEmail());
        $this->assertEquals('John', $message->getFirstName());
        $this->assertEquals('Doe', $message->getLastName());
    }
}