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
    private int $userID;

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
                                                DateTimeImmutable $limitDate,
                                                int $userID){

        parent::__construct($id,  $title,  $description,  $links);
        $this->project = $project;
        $this->taskState = $taskState;
        $this->limitDate = $limitDate;
        $this->userID = $userID;
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

    /**
     * @throws \Exception
     */
    public static function fromArray(array $data): TaskDTO{

    $DataTime = new DateTimeImmutable($data['limitDate']);
    $taskState = State::from($data['taskState']);

    //I get all the links from the data array
    $links = $data['links'];

    //I preemptively create an array which will hold all linkDTOs
    $linksDTOArray = array();

    foreach ($links as $currentLink){//for each linkDTO in links

        $linkDTO =  LinkDTO::fromArray($currentLink);//create linkDTO object from the data

        $linksDTOArray = array_push($linksDTOArray, $linkDTO);//push it into the array
    }



    //TODO adjust the constructor so I can create LinkDTO when getting data  from the endpoint array

        return new self(
            $data['id'] ?? null,
            $data['title'] ,
            $data['description'] ,
            $linksDTOArray,
            $data['project'],
            $taskState,
            $DataTime,
            $data['userID']);
    }

    /**
     * @return int
     */
    public function getUserID(): int
    {
        return $this->userID;
    }

    /**
     * @param int $userID
     */
    public function setUserID(int $userID): void
    {
        $this->userID = $userID;
    }



}