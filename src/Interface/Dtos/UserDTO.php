<?php

namespace App\Interface\Dtos;

use App\Domain\Entities\User;
use JsonSerializable;

class UserDTO implements JsonSerializable
{
    private ?int $id;

    private string $name;

    private string $lastName;

    private string $email;

    private string $password;

    /** Crea un DTO a partir la entidad <code>User</code>
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->id = $user->getId();
        $this->name = $user->getName();
        $this->lastName = $user->getLastName();
        $this->email = $user->getEmail();
        $this->password = $user->getPassword();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): void
    {
        $this->lastName = $lastName;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'lastName' => $this->lastName,
            'email' => $this->email,
            'password' => $this->password
        ];
    }
}