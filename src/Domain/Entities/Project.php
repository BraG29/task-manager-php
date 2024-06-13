<?php


declare(strict_types=1);

namespace App\Domain\Entities;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'Project')]
class Project{

    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue]
    private int $id;

    #[ORM\Column(type: 'string', length: 20)]
    private string $name;

    #[ORM\Column(type: 'string', length: 120)]
    private string $description;

    #[ORM\Column(type: 'string')]
    private bool $available;

    #[ORM\OneToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: "owner_id", referencedColumnName: "id")]
    private User $owner;

    private array $users;

    private array $tasks;


    public function __construct(int $id, string $name, string $description, bool $state, array $users  = [], array $tasks = []) {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->available = $state;
        $this->users = $users;
        $this->tasks = $tasks;
    }

    public function addUsers(User $user): void {
        $this->users[] = $user;
    }

    public function addTasks(Task $task): void {
        $this->tasks[] = $task;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
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

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function isAvailable(): bool
    {
        return $this->available;
    }

    public function setAvailable(bool $available): void
    {
        $this->available = $available;
    }

    public function getUsers(): array
    {
        return $this->users;
    }

    public function setUsers(array $users): void
    {
        $this->users = $users;
    }

    public function getTasks(): array
    {
        return $this->tasks;
    }

    public function setTasks(array $tasks): void
    {
        $this->tasks = $tasks;
    }

    public function getOwner(): User{
        return $this->owner;
    }

    public function setOwner(User $user): void{
        $this->owner = $user;
    }


}