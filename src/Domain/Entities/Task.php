<?php

declare(strict_types=1);

namespace App\Domain\Entities;

use App\Domain\Entities\Enums\State;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Task extends Creatable
{

    #The limit date for the task, it uses a PHP implementation of DateTimeImmutable
    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $limitDate;

    /**
     * <p>
     *  The enum with the states of the task, which are represented by a single
     * character in the string, see State.php
     * </p>
     */

    #[ORM\Column(enumType: State::class)]
    private State $taskState;

    #[ORM\ManyToOne(targetEntity: Project::class, inversedBy: 'tasks')]
    private Project $project;

    //for shody;
    /**
     * @param int|null $id
     * @param string $title
     * @param DateTimeImmutable $limitDate
     * @param string $description
     * @param State $taskState
     */
    public function __construct(int|null          $id,
                                string            $title,
                                DateTimeImmutable $limitDate,
                                string            $description,
                                State            $taskState,
                                Project          $project)
    {
        parent::__construct($id, $title, $description);
        $this->limitDate = $limitDate;
        $this->taskState = $taskState;
        $this->project = $project;
    }

    public function getLimitDate(): DateTimeImmutable
    {
        return $this->limitDate;
    }

    public function setLimitDate(DateTimeImmutable $limitDate): void
    {
        $this->limitDate = $limitDate;
    }

    public function getTaskState(): State
    {
        return $this->taskState;
    }

    public function setTaskState(State $taskState): void
    {
        $this->taskState = $taskState;
    }

    public function getProject(): Project{
        return $this->project;
    }
}