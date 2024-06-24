<?php

namespace App\Application\Controllers;

use App\Domain\Entities\Enums\RoleType;
use App\Domain\Entities\Link;
use App\Domain\Entities\Project;
use App\Domain\Repositories\LinkRepository;
use App\Domain\Repositories\UserRepository;
use App\Interface\Dtos\LinkDTO;
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
            throw new Exception('Project not found');
        }

        $links = $project->getLinks();

        $arraylinks = [];
        foreach ($links as $link) {
            $arraylinks[] = $link->toArray();
        }

        $projectDTOClass = ProjectDTO::class;
        return new $projectDTOClass(
            id: $project->getId(),
            name: $project->getTitle(),
            description: $project->getDescription(),
            state: $project->isAvailable(),
            users: $arraylinks,
            tasks: $project->getTasks()
        );

    }

    /**
     * @param int $userId
     * @return array|null
     */
    public function getProjectDataByUser(int $userId): ?array
    {
        $this->userRepository->findById($userId);

        $user = $this->userRepository->findById($userId);
        $linkSet = $user->getLinks();

        $arraylinks = [];
        $projectDTOArray = [];
        foreach ($linkSet as $link) {
            $arraylinks[] = $link->toArray();
        }
        foreach ($arraylinks as $link) {
            if($link->getCreatable() instanceof Project){
                $project = $link->getCreatable();
                $projectDTOArray[] = new ProjectDTO(
                    id: $project->getId(),
                    name: $project->getTitle(),
                    description: $project->getDescription(),
                    state: $project->isAvailable(),
                    users: $arraylinks,
                    tasks: $project->getTasks()
                );
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
            name: $projectDTO->getName(),
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
            echo "No se pudo dar de alta el Projecto: " . $projectDTO->getName() . " | " . $e->getMessage();
            return 0;
        }
    }

    public function editProject(ProjectDTO $projectDTO): ?int
    {
        if ($projectDTO->getId() !== $this->projectRepository->findById($projectDTO->getId())) {
            return 0;
        }

        $project = new Project(
            id: $projectDTO->getId(),
            name: $projectDTO->getName(),
            description: $projectDTO->getDescription(),
            links: $projectDTO->getUsers(),
            state: $projectDTO->isAvailable(),
        );

        foreach ($projectDTO->getTasks() as $task) {
            $project->addTask($task);
        }

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