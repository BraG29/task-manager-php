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
use Doctrine\ORM\Exception\ORMException;
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
        $LinksDTOArray = [];

        foreach ($links as $link) {
            $LinksDTOArray[] = new LinkDTO(
                id: $link->getId(),
                creationDate: $link->getLinkDate(),
                role: $link->getRole(),
                creatableDTO: null,
                user: new UserDTO($link->getUser())
            );
        }

        $TasksDTOArray = [];

        foreach ($project->getTasks() as $task) {

            $LinksDTOArrayForTask = [];

            foreach ($task->getLinks() as $link) {
                $LinksDTOArrayForTask[] = new LinkDTO(
                    id: $link->getId(),
                    creationDate: $link->getCreationDate(),
                    role: $link->getRole(),
                    creatableDTO: null,
                    user: new UserDTO($link->getUser())
                );
            }
            $TasksDTOArray[] = new TaskDTO(
                id: $task->getId(),
                title: $task->getTitle(),
                description: $task->getDescription(),
                links: $LinksDTOArrayForTask,
                project: $task->getProject(),
                taskState: $task->getTaskState(),
                limitDate: $task->getLimitDate(),
                userID: $task->getUserId()
            );
        }

        $projectDTOClass = ProjectDTO::class;
        return new $projectDTOClass(
            id: $project->getId(),
            name: $project->getTitle(),
            description: $project->getDescription(),
            state: $project->isAvailable(),
            users: $LinksDTOArray,
            tasks: $TasksDTOArray
        );
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

        $linkSet = $user->getLinks();

        $linkDTOArray = [];
        $projectDTOArray = [];

        foreach ($linkSet as $link) {
            if($link->getRole() === RoleType::ADMIN || $link->getRole() === RoleType::EDITOR) {
                if ($link->getCreatable() instanceof Project) {

                    $project = $link->getCreatable();
                    $projectLinks = $project->getLinks();

                    foreach ($projectLinks as $linkOfProject) {
                        $linkDTOArray[] = new LinkDTO(
                            id: null,
                            creationDate: null,
                            role: $linkOfProject->getRole(),
                            creatableDTO: null,
                            user: new UserDTO($linkOfProject->getUser())
                        );
                    }

                    $TasksDTOArray = [];

                    foreach ($project->getTasks() as $task) {
                        $TasksDTOArray[] = new TaskDTO(
                            id: $task->getId(),
                            title: $task->getTitle(),
                            description: $task->getDescription(),
                            links: null,
                            project: null,
                            taskState: null,
                            limitDate: null,
                            userID: null
                        );
                    }

                    $projectDTOArray[] = new ProjectDTO(
                        id: $project->getId(),
                        title: $project->getTitle(),
                        description: $project->getDescription(),
                        links: $linkDTOArray,
                        state: $project->isAvailable(),
                        tasks: $TasksDTOArray
                    );
                }
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

    public function editProject(ProjectDTO $projectDTO): ?int
    {
        if ($this->projectRepository->findById($projectDTO->getId()) === null) {
            echo("Project not found");
            return 0;
        }

        $project = $this->projectRepository->findById($projectDTO->getId());
        $project->setTitle($projectDTO->getTitle());
        $project->setDescription($projectDTO->getDescription());

        return $this->projectRepository->editProject($project);
    }

    public function deleteProject(int $projectId): int
    {
        if ($this->projectRepository->findById($projectId) === null) {
            return 0;
        }
        return $this->projectRepository->deleteProject($projectId);
    }
}