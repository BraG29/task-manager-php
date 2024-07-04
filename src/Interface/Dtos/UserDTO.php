<?php

namespace App\Interface\Dtos;

use App\Domain\Entities\User;
use Doctrine\Common\Collections\ArrayCollection;
use JsonSerializable;

class UserDTO implements JsonSerializable
{
    private int | null $id;

    private string | null $name;

    private string | null $lastName;

    private string | null  $email;

    private string | null $password;

    private bool | null $verified;

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
        $this->verified = $user->isVerified();
    }

    public function getId(): int | null
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getName(): string | null
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getLastName(): string | null
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): void
    {
        $this->lastName = $lastName;
    }

    public function getEmail(): string | null
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getPassword(): string | null
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function isVerified(): bool | null
    {
        return $this->verified;
    }

    public function setVerified(bool $verified): void
    {
        $this->verified = $verified;
    }

    /** Parsea JSON a DTO
     * @param array $data
     * @return UserDTO
     */
    public static function fromArray(array | null $data) : UserDTO
    {

        return new self(
            new User(
                $data['id'] ?? null,
                $data['name'],
                $data['lastName'],
                $data['email'] ?? '',
                $data['password'],
                new ArrayCollection(),
                $data['verified'] ?? ''
            )
        );
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

    public function removeEmail(): void
    {
        $this->email = null;
    }
    public function removePassword(): void
    {

        $this->password = null;
    }
}