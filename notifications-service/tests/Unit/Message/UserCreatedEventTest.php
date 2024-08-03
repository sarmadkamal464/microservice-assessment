<?php

namespace App\Tests\Unit\Message;

use App\Message\UserCreatedEvent;
use PHPUnit\Framework\TestCase;

class UserCreatedEventTest extends TestCase
{
    public function testUserCreatedEvent(): void
    {
        $id = 1;
        $email = 'test@example.com';
        $firstName = 'John';
        $lastName = 'Doe';

        $event = new UserCreatedEvent($id, $email, $firstName, $lastName);

        $this->assertEquals($id, $event->getId());
        $this->assertEquals($email, $event->getEmail());
        $this->assertEquals($firstName, $event->getFirstName());
        $this->assertEquals($lastName, $event->getLastName());
    }
}
