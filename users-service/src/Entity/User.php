<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity]
#[ORM\Table(name: "`user`")]
#[UniqueEntity(fields: ["email"], message: "This email is already in use.")]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'SEQUENCE')]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    #[Assert\NotBlank(message: "Email should not be blank.")]
    #[Assert\Email(message: "The email '{{ value }}' is not a valid email.")]
    private string $email;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank(message: "First name should not be blank.")]
    private string $firstName;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank(message: "Last name should not be blank.")]
    private string $lastName;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;
        return $this;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;
        return $this;
    }
}