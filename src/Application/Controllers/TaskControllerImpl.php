<?php

namespace App\Application\Controllers;

use App\Domain\Entities\Enums\RoleType;
use App\Domain\Entities\Link;
use App\Domain\Entities\Task;
use App\Domain\Repositories\LinkRepository;
use App\Domain\Repositories\ProjectRepository;
use App\Domain\Repositories\TaskRepository;
use App\Domain\Repositories\UserRepository;
use App\Interface\TaskController;
use App\Interface\Dtos\TaskDTO;
use Exception;
use App\Domain\Entities\Enums\State;
use DateTimeImmutable;

class TaskControllerImpl implements TaskController{

    //------------------------------------------------------------------------------------------
    //los controladores van a tener acceso a varios repositorios para propósitos de lógica
    private TaskRepository $taskRepository;
    private ProjectRepository $projectRepository;
    private  LinkRepository $linkRepository;
    private userRepository $userRepository;

    //------------------------------------------------------------------------------------------
    //hay que añadir los repositorios a inyectar al parametro del constructor
    public function __construct(TaskRepository $taskRepository, ProjectRepository $projectRepository, LinkRepository $linkRepository, UserRepository $userRepository){
        $this->taskRepository = $taskRepository;
        $this->projectRepository = $projectRepository;
        $this->linkRepository = $linkRepository;
        $this->userRepository = $userRepository;
    }

    public function getTasksByUser(int $userId): ?array
    {
        //esta era la zarpada function where I had to get the user's projects
        //and compare against the task ones, isn't it?
        //$this->repository->
        // TODO: Implement getTasksByUser() method.
        return null ;
    }

    //NOT TESTED
    public function getTasksByProject(int $projectId): array
    {
        return $this->taskRepository->findTasksByProject($projectId);
    }

    //ask los pibes if it's correct for the TaskDTO to have just projectID instead
    //of an actual projectDTO
    //TODO: finish this function, getting the id from the user through the project ID connected to the task
    public function getTaskById(int $taskId): ?TaskDTO
    {
        $task = $this->taskRepository->findById($taskId);
        return new TaskDTO($task->getId(),
                                        $task->getTitle(),
                                        $task->getDescription(),
                                        $task->getLinks(),
                                        $task->getProject()->getId(),
                                        $task->getTaskState(),
                                        $task->getLimitDate(),
                                        null);
    }

    //BE AWARE as this function needs to give back an int to check
    //the exceptions it might generate
    //I gotta see if THIS is where I check for the project's existence
    //TESTED uwu
    //TODO: I have to link the user that created the task: create Link -> and add it to the user
    //check user has the project that the task belongs to AND it's admin
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
                $taskID = $this->taskRepository->addTask($task);

                //now we try to find the user that is creating the task
                $userToCheck = $this->userRepository->findById($taskDTO->getUserID());

                //we iterate through all the Links the task comes with
                /*
                foreach ($taskDTO->getLinks() as $link) {

                    //we create the link between task and user
                    $linkToAdd = new Link(null,
                                                        new DateTimeImmutable('now'),
                                                        $link->getRole(),
                                                        $task,
                                                        $userToCheck);

                    //we persist the link
                    $this->linkRepository->save($linkToAdd);
                }*/
                $linkToAdd = new Link(null,
                    new DateTimeImmutable('now'),
                    RoleType::ADMIN,
                    $task,
                    $userToCheck);

                //we persist the link
                $this->linkRepository->save($linkToAdd);

                return $taskID;

            } catch (Exception $e) {

                echo "No se pudo dar de alta la tarea: " . $taskDTO->getTitle() . " | " . $e->getMessage();
                return 0;
            }

        }else{
            throw new Exception("No se pudo encontrar el proyecto con ID: " . $taskDTO->getProject() );
            return 0;
        }

    }

    public function updateTask(Task $taskId){

        try {
            $this->taskRepository->updateTask($taskId);
        }catch (Exception $e){

        }

    }

    public function deleteTask(int $taskId)
    {
        // TODO: Implement deleteTask() method.
    }
}