<?php

namespace App\Application\Controllers;

use App\Domain\Entities\Task;
use App\Domain\Repositories\TaskRepository;
use App\Interface\TaskController;
use App\Interface\Dtos\TaskDTO;
use Exception;
use App\Domain\Entities\Enums\State;

class TaskControllerImpl implements TaskController{

    //------------------------------------------------------------------------------------------
    //los controladores van a tener acceso a varios repositorios para propósitos de lógica
    private TaskRepository $repository;

    //------------------------------------------------------------------------------------------
    //hay que añadir los repositorios a inyectar al parametro del constructor
    public function __construct(TaskRepository $taskRepository){
        $this->repository = $taskRepository;
    }

    public function getTasksByUser(int $userId): ?array
    {
        //esta era la zarpada function where I had to get the user's projects
        //and compare against the task ones, isn't it?
        //$this->repository->
        // TODO: Implement getTasksByUser() method.
        return null ;
    }

    public function getTasksByProject(int $projectId): array
    {
        return $this->repository->findTasksByProject($projectId);
    }

    //ask los pibes if it's correct for the TaskDTO to have just projectID instead
    //of an actual projectDTO
    public function getTaskById(int $taskId): ?TaskDTO
    {
        $task = $this->repository->findById($taskId);
        return new TaskDTO($task->getId(),
                                        $task->getTitle(),
                                        $task->getDescription(),
                                        $task->getLinks(),
                                        $task->getProject()->getId(),
                                        $task->getTaskState(),
        $task->getLimitDate());
    }

    //BE AWARE as this function needs to give back an int to check
    //the exceptions it might generate
    public function createTask(TaskDTO $taskDTO): ?int
    {

        $task = new Task(null,
            $taskDTO->getTitle(),
            $taskDTO->getLimitDate(),
            $taskDTO->getLinks(),
            $taskDTO->getDescription(),
            $taskDTO->getTaskState()
        );
        try {
            return $this->repository->addTask($task);

        } catch (Exception $e) {

            echo "No se pudo dar de alta la tarea: " . $taskDTO->getTitle() . " | " . $e->getMessage();
            return 0;
        }
    }

    public function updateTask(Task $taskId)
    {
        // TODO: Implement updateTask() method.
    }

    public function deleteTask(int $taskId)
    {
        // TODO: Implement deleteTask() method.
    }
}