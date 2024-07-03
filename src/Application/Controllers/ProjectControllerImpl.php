<?php

namespace App\Application\Controllers;

use App\Domain\Entities\Enums\RoleType;
use App\Domain\Entities\Link;
use App\Domain\Entities\Project;
use App\Domain\Repositories\LinkRepository;
use App\Domain\Repositories\UserRepository;
use App\Interface\Dtos\LinkDTO;
use App\Interface\Dtos\TaskDTO;
use App\Interface\Dtos\UserDTO;
use App\Interface\ProjectController;
use App\Domain\Repositories\ProjectRepository;
use App\Interface\Dtos\ProjectDTO;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Exception;

class ProjectControllerImpl implements ProjectController {

    private ProjectRepository $projectRepository;
    private UserRepository $userRepository;
    private LinkRepository $linkRepository;

    public function __construct(ProjectRepository  $projectRepository, UserRepository $userRepository, LinkRepository $linkRepository){
        $this->projectRepository = $projectRepository;
        $this->userRepository = $userRepository;
        $this->linkRepository = $linkRepository;
    }

    /**
     * @throws Exception
     */
    public function getProjectData(int $projectId): ?ProjectDTO
    {
        $project = $this->projectRepository->findById($projectId);

        if (!$project) {
            echo('Project not found');
            throw new Exception('Project not found');
        }

        $links = $project->getLinks();
        $linkDTOArray = $this->loadLinkDTOArray($links);
        $TasksDTOArray = $this->loadTaskDTOArray($project->getTasks());

        return new ProjectDTO(
        id: $project->getId(),
        title: $project->getTitle(),
        description: $project->getDescription(),
        links: $linkDTOArray,
        state: $project->isAvailable(),
        tasks: $TasksDTOArray);
    }

    /**
     * @param int $userId
     * @return array|null
     * @throws Exception
     */
    public function getProjectDataByUser(int $userId): ?array
    {
        $user = $this->userRepository->findById($userId);

        if (!$user) {
            echo('User not found');
            throw new Exception('User not found');
        }

        $linkSet = $user->getProjects();
        $projectDTOArray = [];

        foreach ($linkSet as $link) {
            if($link->getRole() === RoleType::ADMIN || $link->getRole() === RoleType::EDITOR) {
                    $projectDTOArray[] = $this->getProjectData($link->getCreatable()->getId());
            }
        }
        return $projectDTOArray;
    }

    /**
     * @throws ORMException
     */
    public function createProject(ProjectDTO $projectDTO, int $userId): ?int
    {
        $newProject = new Project(
            id: null,
            name: $projectDTO->getTitle(),
            description: $projectDTO->getDescription(),
            links: null,
            state: $projectDTO->isAvailable()
        );

        $link = new Link(
            id: null,
            creationDate: new \DateTimeImmutable('now'),
            role: RoleType::ADMIN,
            creatable: $newProject,
            user: $this->userRepository->findById($userId)
        );

        try{
            $this->projectRepository->createProject($newProject);
            $newProject->addLink($link);
            $this->linkRepository->save($link);

            return $newProject->getId();

        }catch (Exception $e){
            echo "No se pudo dar de alta el Projecto: " . $projectDTO->getTitle() . " | " . $e->getMessage();
            return 0;
        }
    }

    /**
     * @throws Exception
     */
    public function editProject(ProjectDTO $projectDTO, int $userId): void
    {
        try {
            $project = $this->projectRepository->findById($projectDTO->getId());
            $user = $this->userRepository->findById($userId);
            if ($project === null) {
                throw new Exception('Projecto no encontrado');
            }
            if ($user === null) {
                throw new Exception('Usuario no encontrado');
            }

            foreach ($project->getTasks() as $task) {
                if($task->getAuthor()->getId() === $userId) {
                    continue;
                }
                throw new Exception('No tiene permisos para editar esta Tarea');
            }

            $flag = false;
            foreach ($project->getLinks() as $link) {
                if(($link->getRole() === RoleType::ADMIN || $link->getRole() === RoleType::EDITOR) && $link->getUser()->getId() === $userId) {
                    $flag = true;
                    break;
                }
            }

            if($flag === false){
                throw new Exception('No tiene permisos para editar este Projecto');
            }

            if($projectDTO->getTitle() != null){
                $project->setTitle($projectDTO->getTitle());
            }

            if($projectDTO->getDescription() != null){
                $project->setDescription($projectDTO->getDescription());
            }

            $this->projectRepository->editProject($project);
        }
        catch(Exception $e){
            throw new Exception( "No se pudo editar el Projecto: " . $projectDTO->getTitle() . " | " . $e->getMessage());
        }
    }
    /**
     * @throws OptimisticLockException
     * @throws ORMException
     * @throws Exception
     */
    public function deleteProject(int $projectId, int $userId): void
    {
        try {
            if ($this->projectRepository->findById($projectId) === null) {
                throw new Exception('Projecto no encontrado');
            }
            if ($this->userRepository->findById($userId) === null) {
                throw new Exception('Usuario no encontrado');
            }

            $project = $this->projectRepository->findById($projectId);

            foreach ($project->getLinks() as $link) {
                if ($link->getRole() === RoleType::ADMIN && $link->getUser()->getId() === $userId) {
                    $this->projectRepository->deleteProject($link->getCreatable()->getId());
                    return;
                }
                else {
                    throw new Exception('Usuario no autorizado');
                }
            }
        }
        catch (Exception $e) {
            throw new Exception( "No se pudo borrar el Projecto: " . $projectId . " | " . $e->getMessage());
        }
    }

    // UTILS -----------------------------------------------------------------------------------------------------------

    public function loadLinkDTOArray(Collection $links): array{

        $linkDTOArray = [];
        foreach ($links as $link) {
            $userDTO = new UserDTO($link->getUser());
            $userDTO->removePassword();
            $userDTO->removeEmail();

            $linkDTOArray[] = new LinkDTO(
                id: $link->getId(),
                creationDate: null,
                role: $link->getRole(),
                creatableDTO: null,
                user: $userDTO
            );
        }
        return $linkDTOArray;

    }

    public function loadTaskDTOArray(Collection $tasks): array{

        $TasksDTOArray = [];

        foreach ($tasks as $task) {
            $TasksDTOArray[] = new TaskDTO(
                id: $task->getId(),
                title: $task->getTitle(),
                description: $task->getDescription(),
                links: $this->loadLinkDTOArray($task->getLinks()),
                project: $task->getProject() ? $task->getProject()->getId() : null,
                taskState: $task->getTaskState(),
                limitDate: $task->getLimitDate(),
                userID:null
            );
        }
        return $TasksDTOArray;
    }


}