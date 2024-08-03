<?php 
namespace App\Tests\Unit\MessageHandler;

use App\Message\UserCreatedEvent;
use App\MessageHandler\UserCreatedHandler;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class UserCreatedHandlerTest extends TestCase
{
    public function testInvoke(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())
            ->method('info')
            ->with(
                'User created',
                $this->callback(function ($context) {
                    return $context['id'] === 1 &&
                        $context['email'] === 'test@example.com' &&
                        $context['firstName'] === 'John' &&
                        $context['lastName'] === 'Doe';
                })
            );

        $handler = new UserCreatedHandler($logger);
        $event = new UserCreatedEvent(1, 'test@example.com', 'John', 'Doe');

        $handler->__invoke($event);
    }
}