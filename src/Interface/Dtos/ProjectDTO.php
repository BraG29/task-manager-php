<?php

namespace App\Interface\Dtos;

use App\Domain\Entities\Project;
use JsonSerializable;

class ProjectDTO extends CreatableDTO{

    private bool | null $state;
    private array | null $tasks;


    public function __construct(int | null $id,
                                string | null $title,
                                string | null $description,
                                array | null $links,
                                bool | null $state,
                                array |null $tasks = []){
        parent::__construct($id, $title, $description, $links); // Call the parent constructor
        $this->state = $state;
        $this->tasks = $tasks;
    }

    public static function fromArray(object|array|null $data): ?ProjectDTO
    {
        if ($data === null) {
            return null;
        }

        $state = null;
        if($data['state'] != null){
            $state = data['state'];
        }

        $title = null;
        if($data['title'] != null){
            $title = data['title'];
        }

        $description = null;
        if($data['description'] != null){
            $description = data['description'];
        }

        $links = [];

        foreach ($data['links'] as $link){
            $links[] = LinkDTO::fromArray($link);
        }

        return new ProjectDTO(
            id: $data['id'],
            title: $title,
            description: $description,
            links: $links,
            state: $state,
            tasks: null
        );
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTitle(): string | null
    {
        return $this->title;
    }

    public function getDescription(): string | null
    {
        return $this->description;
    }

    public function isAvailable(): bool | null
    {
        return $this->state;
    }

    public function getLinks(): array
    {
        return $this->links;
    }
    public function getUserById(int $userId): array | null //unused and probably unnecessary
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

    public function getTasks(): array | null
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
            'links' => $this->links,
            'tasks' => $this->tasks
        ];
    }

    public function setId(mixed $projectId): void
    {
        $this->id = $projectId;
    }
}