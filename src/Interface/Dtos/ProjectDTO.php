<?php

namespace App\Interface\Dtos;

use App\Domain\Entities\Project;
use JsonSerializable;

class ProjectDTO implements JsonSerializable{

    private int $id;
    private string $name;
    private string $description;
    private bool $available;
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

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function isAvailable(): bool
    {
        return $this->available;
    }

    public function getUsers(): array
    {
        return $this->users;
    }

    public function getTasks(): array
    {
        return $this->tasks;
    }


    public function jsonSerialize():array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'state' => $this->available,
            'userList' => $this->users,
            'taskList' => $this->tasks
        ];
    }
}