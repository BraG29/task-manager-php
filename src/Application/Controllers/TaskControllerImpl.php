<?php

namespace App\Application\Controllers;

use App\Domain\Entities\Task;
use App\Domain\Repositories\ProjectRepository;
use App\Domain\Repositories\TaskRepository;
use App\Interface\TaskController;
use App\Interface\Dtos\TaskDTO;
use Exception;
use App\Domain\Entities\Enums\State;

class TaskControllerImpl implements TaskController{

    //------------------------------------------------------------------------------------------
    //los controladores van a tener acceso a varios repositorios para propósitos de lógica
    private TaskRepository $taskRepository;
    private ProjectRepository $projectRepository;

    //------------------------------------------------------------------------------------------
    //hay que añadir los repositorios a inyectar al parametro del constructor
    public function __construct(TaskRepository $taskRepository, ProjectRepository $projectRepository){
        $this->taskRepository = $taskRepository;
        $this->projectRepository = $projectRepository;
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
        $task = $this->taskRepository->findById($taskId);
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
    //I gotta see if THIS is where I check for the project's existence
    public function createTask(TaskDTO $taskDTO): ?int
    {
        //We find the project that the task belongs to
        $projectTask = $this->projectRepository->findById($taskDTO->getProject());

        //we control that the project is not null
        if ($projectTask != null) {

            try { //let's try to create & persist the task
                $task = new Task(null,
                    $taskDTO->getTitle(),
                    $taskDTO->getLimitDate(),
                    $taskDTO->getDescription(),
                    $taskDTO->getTaskState(),
                    $projectTask);

                //we try to persist the task
                return $this->taskRepository->addTask($task);

            } catch (Exception $e) {

                echo "No se pudo dar de alta la tarea: " . $taskDTO->getTitle() . " | " . $e->getMessage();
                return 0;
            }

        }else{
            throw new Exception("No se pudo encontrar el proyecto con ID: " . $taskDTO->getProject() );
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