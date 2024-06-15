<?php

namespace App\Interface\Dtos;

use App\Domain\Entities\Enums\State;
use App\Domain\Entities\Project;
use DateTimeImmutable;

class TaskDTO extends CreatableDTO{
    private DateTimeImmutable $limitDate;
    private State $taskState;
    private Project $project;

    /**
     * @param Project $project
     * @param State $taskState
     * @param DateTimeImmutable $limitDate
     */
    public function __construct(
                                                int $id,
                                                string $title,
                                                string $description,
                                                array $links,
                                                Project $project,
                                                State $taskState,
                                                DateTimeImmutable $limitDate){

        parent::__construct($id,  $title,  $description,  $links);
        $this->project = $project;
        $this->taskState = $taskState;
        $this->limitDate = $limitDate;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getLimitDate(): DateTimeImmutable
    {
        return $this->limitDate;
    }

    /**
     * @param DateTimeImmutable $limitDate
     */
    public function setLimitDate(DateTimeImmutable $limitDate): void
    {
        $this->limitDate = $limitDate;
    }

    /**
     * @return State
     */
    public function getTaskState(): State
    {
        return $this->taskState;
    }

    /**
     * @param State $taskState
     */
    public function setTaskState(State $taskState): void
    {
        $this->taskState = $taskState;
    }

    /**
     * @return Project
     */
    public function getProject(): Project
    {
        return $this->project;
    }

    /**
     * @param Project $project
     */
    public function setProject(Project $project): void
    {
        $this->project = $project;
    }


}