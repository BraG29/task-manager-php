<?php

namespace App\Interface\Dtos;

use App\Domain\Entities\Enums\State;
use App\Domain\Entities\Task;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use JsonSerializable;

class TaskDTO extends CreatableDTO{
    private DateTimeImmutable $limitDate;
    private State $taskState;
    private int $project;

    /**
     * @param int $id
     * @param string $title
     * @param string $description
     * @param array $links
     * @param int $project
     * @param State $taskState
     * @param DateTimeImmutable $limitDate
     */
    public function __construct(
                                                int | null $id,
                                                string $title,
                                                string $description,
                                                array | null $links,
                                                int $project,
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
     * @return int
     */
    public function getProject(): int
    {
        return $this->project;
    }

    /**
     * @param int $project
     */
    public function setProject(int $project): void
    {
        $this->project = $project;
    }

#[\ReturnTypeWillChange]
public function jsonSerialize(): array{
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'links' => $this->links,
            'project' => $this->project,
            'limitDate' => $this->limitDate,
            'taskState' => $this->taskState

        ];
}

//ublic function __construct(
//     int $id,
//     string $title,
//     string $description,
//     array $links,
//     int $project,
//     State $taskState,
//     DateTimeImmutable $limitDate ): TaskDTO

/*
 *                 $data['id']?? null,
                $data['title'],
                $data['description'],
                new ArrayCollection($data['links']),
                $data['project'],
                $data['taskState'],
                $data['limitDate'],

            new Task(
                $data['id'] ?? null,
                $data['title'] ,
                $DataTime ,
                $data['description'] ,
                $taskState,
                $data['project']
            )
        );
 */

    /**
     * @throws \Exception
     */
    public static function fromArray(array $data): TaskDTO{

    $DataTime = new DateTimeImmutable($data['limitDate']);
    $taskState = State::from($data['taskState']);

        return new self(
            $data['id'] ?? null,
            $data['title'] ,
            $data['description'] ,
            $data['links'] ?? null,
                $data['project'],
            $taskState,
            $DataTime);
    }



}