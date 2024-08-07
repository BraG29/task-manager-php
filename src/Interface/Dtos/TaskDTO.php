<?php

namespace App\Interface\Dtos;

use App\Domain\Entities\Enums\State;
use App\Domain\Entities\Task;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Exception;
use JsonSerializable;
use ReturnTypeWillChange;

class TaskDTO extends CreatableDTO{
    private DateTimeImmutable | null $limitDate;
    private State | null $taskState;
    private int |null $project;
    private int | null $userID;

    /**
     * @param int|null $id
     * @param string|null $title
     * @param string|null $description
     * @param array|null $links
     * @param int|null $project
     * @param State|null $taskState
     * @param DateTimeImmutable|null $limitDate
     * @param int|null $userID
     */
    public function __construct(
                                                int | null $id,
                                                string | null $title,
                                                string | null $description,
                                                array | null $links,
                                                int | null  $project,
                                                State | null $taskState,
                                                DateTimeImmutable | null $limitDate,
                                                int | null $userID){

        parent::__construct($id,  $title,  $description,  $links);
        $this->project = $project;
        $this->taskState = $taskState;
        $this->limitDate = $limitDate;
        $this->userID = $userID;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getLimitDate(): DateTimeImmutable | null
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
    public function getTaskState(): State | null
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
    public function getProject(): int | null
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

#[ReturnTypeWillChange]
public function jsonSerialize(): array{
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'links' => $this->links,
            'project' => $this->project,
            'limitDate' => $this->limitDate,
            'taskState' => $this->taskState,
            'userID' => $this->userID
        ];
}

    /**
     * @throws Exception
     */
    public static function fromArray(array $data): TaskDTO{

    if($data['limitDate'] != null){
        $DataTime = new DateTimeImmutable($data['limitDate']);
    }

    $taskState = null;
    if ($data['taskState'] != null || $data['taskState'] == 0) {
        $taskState = State::from($data['taskState']);
    }

    //I get all the links from the data array
    $links = $data['links'];

    //I preemptively create an array which will hold all linkDTOs
    $linksDTOArray = array();

    if(!empty($links)){
        foreach ($links as $currentLink){//for each linkDTO in links

            $linkDTO =  LinkDTO::fromArray($currentLink);//create linkDTO object from the data

            $linksDTOArray = array_push($linksDTOArray, $linkDTO);//push it into the array
        }
    }


    //TODO adjust the constructor so I can create LinkDTO when getting data  from the endpoint array

        return new self(
            id: $data['id'] ?? null,
            title: $data['title'] ?? null,
            description: $data['description'] ?? null,
            links: $linksDTOArray,
            project: $data['project'] ?? null,
            taskState: $taskState ?? null,
            limitDate: $DataTime ?? null,
            userID: $data['userID'] ?? null);
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