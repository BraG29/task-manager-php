<?php

declare(strict_types=1);

namespace App\Domain\Entities;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'User')]
class User
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue]
    private ?int $id;

    #[ORM\Column(type: 'string')]
    private string $name;

    #[ORM\Column(name: "last_name", type: 'string')]
    private string $lastName;

    #[ORM\Column(type: 'string', unique: true)]
    private string $email;

    #[ORM\Column(type: 'string')]
    private string $password;

    #[ORM\OneToMany(targetEntity: Link::class, mappedBy: 'user')]
    private Collection $links;

    #[ORM\Column(type: 'boolean')]
    public bool $verified;

    /**
     * @param int|null $id
     * @param string $name
     * @param string $lastName
     * @param string $email
     * @param string $password
     * @param Collection $links
     */
    public function __construct(?int   $id,
                                string     $name,
                                string     $lastName,
                                string     $email,
                                string     $password,
                                Collection $links,
                                bool $verified)
    {
        $this->id = $id;
        $this->name = $name;
        $this->lastName = $lastName;
        $this->email = $email;
        $this->password = $password;
        $this->links = $links;
        $this->verified = $verified;
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

    public function getLinks(): Collection
    {
        return $this->links;
    }

    public function setLinks(Collection $links): void
    {
        $this->links = $links;
    }

    public function isVerified(): bool
    {
        return $this->verified;
    }

    public function setVerified(bool $verified): void
    {
        $this->verified = $verified;
    }

    public function getProjects(): array
    {
        $links = $this->getLinks();
        $projects = [];
        foreach ($links as $link) {
            if($link->getCreatable() instanceof Project){
                $projects[] = $link;
            }
        }
        return $projects;
    }

    public function getTasks(): array
    {
        $links = $this->getLinks();
        $tasks = [];
        foreach ($links as $link) {
            if($link->getCreatable() instanceof Task){
                $tasks[] = $link;
            }
        }
        return $tasks;
    }

}
