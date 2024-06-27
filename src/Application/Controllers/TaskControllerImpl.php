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
use Doctrine\ORM\Exception\ORMException;
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
    public function __construct(TaskRepository $taskRepository,
                                                ProjectRepository $projectRepository,
                                                LinkRepository $linkRepository,
                                                UserRepository $userRepository){

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

    /**
     * @throws Exception
     */
    public function getTasksByProject(int $projectId): array
    {
        try {
            //we call the repository to get the tasks for a given project ID
            $tasks = $this->taskRepository->findTasksByProject($projectId);

            if($tasks == null){
                //throw Exception if we don't find any
                throw new Exception("No hay tareas para este proyecto con ID: " . $projectId);
            }

            //the array we will fill with the jsons of the taskDTOs of the task we found
            $tasksDTO = [];

            foreach ($tasks as $task){//for each task I got

                //I make the link array so the taskDTO constructor doesn't die
                $links = $task->getLinks()->toArray();

                //we create the taskDTO from the task data
                $taskDTO = new TaskDTO($task->getId(),
                    $task->getTitle(),
                    $task->getDescription(),
                    $links,
                    $task->getProject()->getId(),
                    $task->getTaskState(),
                    $task->getLimitDate(),
                    null);


                //we fill the array with the json form of the taskDTO we got from the task
                $tasksDTO[] = $taskDTO->jsonSerialize();
            }

            return $tasksDTO;

        }catch(Exception $e){
            throw $e;
        }
    }


    //TESTED UWU
    //TODO: properly add the user's creator ID into the task I am returning -> LUCAS SAID NO
    //TODO: Ask los pibes how to get the array of links for said task within this function
    /**
     * @throws Exception
     */
    public function getTaskById(int $taskId): ?TaskDTO
    {
        //we search the task for the ID it has
        $task = $this->taskRepository->findById($taskId);

        //we control that the task we get is not null
        if ($task == null){
            throw new Exception("No se pudo encontrar una tarea con ID: " . $taskId);
        }

        //we form the arrays of the links so that the TaskDTO constructor doesn't die
        $links = $task->getLinks()->toArray();

        //we create the taskDTO from the task data
        return new TaskDTO($task->getId(),
        $task->getTitle(),
        $task->getDescription(),
        $links,
        $task->getProject()->getId(),
        $task->getTaskState(),
        $task->getLimitDate(),
        null);
    }



    // TESTED :D
    //TODO: add a function that lets me add an array of users to a given task

    /**
     * @throws ORMException
     * @throws Exception
     */
    public function createTask(TaskDTO $taskDTO): ?int
    {
        //We find the project that the task belongs to
        $projectTask = $this->projectRepository->findById($taskDTO->getProject());

        try { //let's try to create & persist the task

            //we control that the project is not null
            if ($projectTask == null) {
                throw new Exception("No se pudo encontrar el proyecto con ID: " . $taskDTO->getProject() );
            }
            //we create the domain object of the Task to add
            $task = new Task(null,
                $taskDTO->getTitle(),
                $taskDTO->getLimitDate(),
                $taskDTO->getDescription(),
                $taskDTO->getTaskState(),
                $projectTask);

            //now we try to find the user that is creating the task
            $userToCheck = $this->userRepository->findById($taskDTO->getUserID());
            if ($userToCheck == null) {
                throw new Exception("No se pudo encontrar el usuario con ID: " . $taskDTO->getUserID());
            }

            //I get all the links of that given project so I can check User privileges
            $userLinks = $userToCheck->getLinks();

            foreach ($userLinks as $link) {

                //if the creatable we find in the user's link is the same as the project one
                if(  $link->getCreatable()->getId() == $projectTask->getId() ){

                    //check privileges
                    if ($link->getRole() == RoleType::ADMIN ||  $link->getRole() == RoleType::EDITOR){

                        //create the link domain object
                        $linkToAdd = new Link(null,
                            new DateTimeImmutable('now'),
                            RoleType::ADMIN,
                            $task,
                            $userToCheck);

                        //we try to persist the task
                        $taskID = $this->taskRepository->addTask($task);

                        //we persist the link
                        $this->linkRepository->save($linkToAdd);

                        return $taskID;
                    }
                }
            }
            throw new Exception("el usuario: " . $userToCheck->getName() . " no esta autorizado para este proyecto: " . $projectTask->getTitle());


        } catch (Exception $e) {

            echo "No se pudo dar de alta la tarea: " . $taskDTO->getTitle();
            echo "\n";
            echo "-----------------------------------------------------------------------------------------------------" . "\n";
            echo "Razón: " . $e->getMessage();
            throw $e;
            //return 0;
        }
    }

    //check the privileges of the user who is deleting the task
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