<?php

namespace App\Interface\Dtos;

use App\Domain\Entities\Project;
use JsonSerializable;

class ProjectDTO extends CreatableDTO{


    private ?bool $state;
    private ?array $tasks;


    public function __construct(int | null $id,
                                string | null $title,
                                string | null $description,
                                array | null $links,
                                ?bool $state,
                                ?array $tasks = []){
        $this->id = $id;
        $this->title = $title;
        $this->description = $description;
        $this->state = $state;
        $this->links = $links;
        $this->tasks = $tasks;
    }

    public static function fromArray(object|array|null $data): ?ProjectDTO
    {
        if ($data === null) {
            return null;
        }
        return new ProjectDTO(
            $data['id'],
            $data['name'],
            $data['description'],
            $data['state'],
            $data['userList'],
            $data['taskList']
        );
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function isAvailable(): bool
    {
        return $this->state;
    }

    public function getLinks(): array
    {
        return $this->links;
    }
    public function getUserById(int $userId): ?array //unused and probably unnecessary
    {

//        foreach ($this->links as $user) {
//            if ($user['id'] === $userId) {
//                return $user;
//            }
//            else {
//                return null;
//            }
//        }
       return null;
    }
    public function getTaskById(int $taskId): ?array //unused and probably unnecessary
    {

//        foreach ($this->tasks as $task) {
//            if ($task['id'] === $taskId) {
//                return $task;
//            }
//            else {
//                return null;
//            }
//        }
        return null;
    }
    public function getFirstUserId(): ?int{
        return $this->users[0]['id'];
    }

    public function getTasks(): ?array
    {
        return $this->tasks;
    }


    public function jsonSerialize():array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'state' => $this->state,
            'linkList' => $this->links,
            'taskList' => $this->tasks
        ];
    }

    public function setId(mixed $projectId): void
    {
        $this->id = $projectId;
    }
}