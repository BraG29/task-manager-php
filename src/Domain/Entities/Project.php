<?php


declare(strict_types=1);

namespace App\Domain\Entities;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Project extends Creatable {

    #[ORM\Column(type: 'string')]
    private bool $available;

    #[ORM\OneToMany(targetEntity: Task::class, mappedBy: 'project')]
    private array $tasks;

    public function __construct(int    $id,
                                string $name,
                                string $description,
                                array  $links,
                                bool   $state,
                                array  $tasks)
    {
        parent::__construct($id, $name, $description, $links);
        $this->available = $state;
        $this->tasks = $tasks;
    }

    public function addLink(Link $link) : void
    {
        $this->links[] = $link;
    }

    public function addTask(Task $task): void {
        $this->tasks[] = $task;
    }

    public function isAvailable(): bool
    {
        return $this->available;
    }

    public function setAvailable(bool $available): void
    {
        $this->available = $available;
    }

    public function getTasks(): array
    {
        return $this->tasks;
    }

    public function setTasks(array $tasks): void
    {
        $this->tasks = $tasks;
    }

}