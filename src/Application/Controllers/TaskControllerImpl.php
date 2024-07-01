<?php

namespace App\Application\Controllers;

use App\Domain\Entities\Enums\RoleType;
use App\Domain\Entities\Link;
use App\Domain\Entities\Task;
use App\Domain\Repositories\LinkRepository;
use App\Domain\Repositories\ProjectRepository;
use App\Domain\Repositories\TaskRepository;
use App\Domain\Repositories\UserRepository;
use App\Interface\Dtos\UserDTO;
use App\Interface\TaskController;
use App\Interface\Dtos\TaskDTO;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
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

    //TESTED uwu

    /**
     * @throws Exception
     */
    public function getTasksByUser(int $userId): ?array
    {
        try {
            //we search for the user we want to get the tasks from
            $userToSearch = $this->userRepository->findById($userId);

            if ($userToSearch == null){
                throw new Exception("No se pudo encontrar el usuario con ID: " . $userId . " para revisar sus tareas");
            }



            //we get the links from the user
            /** @var ArrayCollection|Link[] $links */
            $userLinks = $userToSearch->getLinks();

            //if (empty($userLinksCheck)){
            if (count($userLinks) < 2) {
                throw new Exception(" el usuario: " . $userToSearch->getName() . " no tiene ninguna tarea asignada");
            }

            //we prepare the array that we will be returning with the TaskDTOs
            $tasksDTO = [];

            //we iterate to get all the links that are tasks
            foreach ($userLinks as $link){

                //I get the creatable from the link
                $linkCreatable = $link->getCreatable();

                //if the creatable is a Task
                if ( $linkCreatable instanceof Task){

                    //I make the link array so the taskDTO constructor doesn't die
                    $linksFromTask = $linkCreatable->getLinks();

                    //we make the array we will fill with the users of the tasks we find
                    $usersDTO = [];

                    foreach ($linksFromTask as $taskLink){
                        //we get the User from the link of the task
                        $userLink = $taskLink->getUser();

                        //we create the userDTO from it
                        $userDTO = new UserDTO($userLink);

                        //we fill the array with the json form of the taskDTO we got from the task
                        $usersDTO[] = $userDTO->jsonSerialize();

                    }


                    //we create the taskDTO from the task data
                    $taskDTO = new TaskDTO($linkCreatable->getId(),
                        $linkCreatable->getTitle(),
                        $linkCreatable->getDescription(),
                        $usersDTO,
                        $linkCreatable->getProject()->getId(),
                        $linkCreatable->getTaskState(),
                        $linkCreatable->getLimitDate(),
                        null);


                    //we fill the array with the json form of the taskDTO we got from the task
                    $tasksDTO[] = $taskDTO->jsonSerialize();
                }
            }
            return $tasksDTO;

        }catch (Exception $e){
            throw new Exception($e->getMessage());
        }
    }

    //TESTED UWU
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
                $links = $task->getLinks();

                //we make the array we will fill with the users of the tasks we find
                $usersDTO = [];

                foreach ($links as $link){
                    //we get the User from the link of the task
                    $userLink = $link->getUser();

                    //we create the userDTO from it
                    $userDTO = new UserDTO($userLink);

                    //we fill the array with the json form of the taskDTO we got from the task
                    $usersDTO[] = $userDTO->jsonSerialize();

                }

                //we create the taskDTO from the task data
                $taskDTO = new TaskDTO($task->getId(),
                    $task->getTitle(),
                    $task->getDescription(),
                    $usersDTO,
                    $task->getProject()->getId(),
                    $task->getTaskState(),
                    $task->getLimitDate(),
                    0);


                //we fill the array with the json form of the taskDTO we got from the task
                $tasksDTO[] = $taskDTO->jsonSerialize();
            }

            return $tasksDTO;

        }catch(Exception $e){
            throw $e;
        }
    }


    //TESTED UWU
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

        //we get the links from  the task so we can get the users
        $links = $task->getLinks();

        //we create the array we will be filling with the task's users
        $usersDTO = [];
        foreach ($links as $link) {

            //we get the user from the task's link
            $userLink = $link->getUser();

            //we create the user DTO from the user domain object
            $userDTO = new UserDTO($userLink);

            //we fill the array with the json form of the taskDTO we got from the task
            $usersDTO[] = $userDTO->jsonSerialize();
        }

        //we create the taskDTO from the task data
        return new TaskDTO($task->getId(),
        $task->getTitle(),
        $task->getDescription(),
        $usersDTO, //this is NOT gonna work lmao. . .
        $task->getProject()->getId(),
        $task->getTaskState(),
        $task->getLimitDate(),
        null);
    }



    // TESTED UWU
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

    //TODO: EN QUE MOMENTO DE INCONSCIENCIA COLECTIVA
    //TODO: SE ME DIO POR RECIBIR UN OBJETO DE DOMINIO DESDE EL ENDPOINT?!?!?!?!?!?!?!?
    //TODO: APARTE EL NOMBRE ESTÁ RE ROTO AMIGO NOOOOO
    //check the privileges of the user who is deleting the task
    public function updateTask(Task $taskId){

        try {
            $this->taskRepository->updateTask($taskId);
        }catch (Exception $e){

        }

    }




    /**
     * @throws OptimisticLockException
     * @throws ORMException
     * @throws Exception
     */
    public function deleteTask(int $taskId, int $userId){

        try {
            //we find the user that wants to delete the task
            $userToCheck = $this->userRepository->findById($userId);

            if ($userToCheck == null) {
                throw new Exception("No se pudo encontrar el usuario con ID: " . $userId);
            }

            //we find the task we will be deleting
            $taskToDelete = $this->taskRepository->findById($taskId);

            if ($taskToDelete == null) {
                throw new Exception("No se pudo encontrar la tarea con ID: " . $taskId);
            }

            //I get all the links of the user
            $userLinks = $userToCheck->getLinks();



            foreach ($userLinks as $link) {

                //if the creatable of the user we find is the same as the task's ID
                if(  $link->getCreatable()->getId() == $taskToDelete->getId() ){

                    //check privileges
                    if ($link->getRole() == RoleType::ADMIN){

                        //we DELETE the task
                        $this->taskRepository->deleteTask($taskToDelete);
                        return;
                    }
                    else{
                        throw new Exception("No se tienen los permisos correspondientes para eliminar la tarea con ID: " . $taskToDelete->getId());
                    }
                }
            }

            throw new Exception("No se encontró el vinculo de la tarea con ID: " . $taskId . " para el usuario con ID: " . $userToCheck->getId());

        }catch (Exception $e){
            throw new Exception($e->getMessage());
        }
    }
}