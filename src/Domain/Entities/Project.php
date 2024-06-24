<?php


declare(strict_types=1);

namespace App\Domain\Entities;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Project extends Creatable {

    #[ORM\Column(type: 'string',)]
    private bool $available;

    #[ORM\OneToMany(targetEntity: Task::class, mappedBy: 'project',cascade: ['remove'], orphanRemoval: true)]
    private Collection $tasks;

    public function __construct(?int    $id,
                                string $name,
                                string $description,
                                ?array  $links,
                                bool   $state)
    {
        parent::__construct($id, $name, $description, $links);
        $this->available = $state;
        $this->tasks = new ArrayCollection();
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

    public function getTasks(): Collection
    {
        return $this->tasks;
    }

    public function setTasks(Collection $tasks): void
    {
        $this->tasks = $tasks;
    }


}