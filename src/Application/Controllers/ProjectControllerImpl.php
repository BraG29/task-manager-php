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

    public function getProjectData(int $projectId)
    {
        // TODO: Implement getProjectData() method.
    }

    public function getProjectDataByUser(int $userId)
    {
        // TODO: Implement getProjectDataByUser() method.
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

    public function editProject(ProjectDTO $projectDTO)
    {
        // TODO: Implement editProject() method.
    }

    public function deleteProject(int $projectId)
    {
        // TODO: Implement deleteProject() method.
    }
}