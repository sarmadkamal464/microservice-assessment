<?php

namespace App\MessageHandler;

use App\Message\UserCreatedEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class UserCreatedHandler
{
    public function __construct(private LoggerInterface $logger)
    {}

    public function __invoke(UserCreatedEvent $message): void
    {
        $this->logger->info('User created', [
            'id' => $message->getId(),
            'email' => $message->getEmail(),
            'firstName' => $message->getFirstName(),
            'lastName' => $message->getLastName(),
        ]);
    }
}