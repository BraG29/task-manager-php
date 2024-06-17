<?php

declare(strict_types=1);

namespace App\Domain\Entities;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'Token')]
class Token{

    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue]
    private ?int $id;

    #[ORM\Column(type: 'string')]
    private string $token;

    #[ORM\OneToOne(targetEntity: User::class, inversedBy: 'token')]
    private User $user;

    /**
     * @param int|null $id
     * @param string $token
     */
    public function __construct(?int $id, string $token)
    {
        $this->id = $id;
        $this->token = $token;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function setToken(string $token): void
    {
        $this->token = $token;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
    }




}