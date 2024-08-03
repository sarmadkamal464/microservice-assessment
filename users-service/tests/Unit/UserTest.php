<?php

namespace App\Tests\Unit;

use App\Entity\User;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints as Assert;

class UserTest extends TestCase
{
    public function testUserCreation(): void
    {
        $user = new User();
        $user->setEmail('test@example.com')
            ->setFirstName('John')
            ->setLastName('Doe');

        $this->assertEquals('test@example.com', $user->getEmail());
        $this->assertEquals('John', $user->getFirstName());
        $this->assertEquals('Doe', $user->getLastName());
        $this->assertNull($user->getId());
    }

    public function testUserValidation(): void
    {
        $validator = Validation::createValidator();

        $user = new User();
        
        // Define validation constraints manually
        $constraints = new Assert\Collection([
            'email' => [
                new Assert\NotBlank(),
                new Assert\Email(),
            ],
            'firstName' => new Assert\NotBlank(),
            'lastName' => new Assert\NotBlank(),
        ]);

        // Test with invalid data
        $user->setEmail('invalid-email')
            ->setFirstName('')
            ->setLastName('');

        $violations = $validator->validate([
            'email' => $user->getEmail(),
            'firstName' => $user->getFirstName(),
            'lastName' => $user->getLastName(),
        ], $constraints);

        $this->assertCount(3, $violations);

        // Test with valid data
        $user->setEmail('valid@example.com')
            ->setFirstName('John')
            ->setLastName('Doe');

        $violations = $validator->validate([
            'email' => $user->getEmail(),
            'firstName' => $user->getFirstName(),
            'lastName' => $user->getLastName(),
        ], $constraints);

        $this->assertCount(0, $violations);
    }
}
