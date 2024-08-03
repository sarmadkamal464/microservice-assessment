<?php

namespace App\Tests\Functional;

use App\Message\UserCreatedEvent;
use App\MessageHandler\UserCreatedHandler;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Messenger\Envelope;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class UserCreatedEventFunctionalTest extends KernelTestCase
{
    private UserCreatedHandler $handler;
    private LoggerInterface $logger;
    private ContainerInterface $container;

    protected function setUp(): void
    {
        self::bootKernel();

        $this->container = static::getContainer();
        $this->handler = $this->container->get(UserCreatedHandler::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        
        // Replace the logger in the handler with our mock
        $reflection = new \ReflectionClass($this->handler);
        $loggerProperty = $reflection->getProperty('logger');
        $loggerProperty->setAccessible(true);
        $loggerProperty->setValue($this->handler, $this->logger);
    }

    public function testUserCreatedEventIsProcessed(): void
    {
        $event = new UserCreatedEvent(1, 'test@example.com', 'John', 'Doe');

        // Set expectations for the logger
        $this->logger->expects($this->once())
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

        // Process the message
        ($this->handler)($event);
    }
}