<?php

namespace App\Message;


class UserCreatedEvent
{
    public function __construct(
        private int $id,
        private string $email,
        private string $firstName,
        private string $lastName
    ) {}

    public function getId(): int
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }
}