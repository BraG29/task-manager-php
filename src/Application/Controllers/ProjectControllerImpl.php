<?php

namespace App\Application\Controllers;

use App\Domain\Entities\Project;
use App\Interface\ProjectController;
use App\Domain\Repositories\ProjectRepository;
use App\Interface\Dtos\ProjectDTO;
use Doctrine\Common\Collections\ArrayCollection;
use Exception;

class ProjectControllerImpl implements ProjectController {

    private ProjectRepository $projectRepository;

    public function __construct(ProjectRepository  $projectRepository){
        $this->projectRepository = $projectRepository;
    }

    public function getProjectData(int $projectId)
    {
        // TODO: Implement getProjectData() method.
    }

    public function getProjectDataByUser(int $userId)
    {
        // TODO: Implement getProjectDataByUser() method.
    }

    public function createProject(ProjectDTO $projectDTO): ?int
    {
        $newProject = new Project(
            id: $projectDTO->getId(),
            name: $projectDTO->getName(),
            description: $projectDTO->getDescription(),
            links: (array)null,
            state: $projectDTO->isAvailable()
        );
        try{
            return $this->projectRepository->createProject($newProject);
        }catch (Exception $e){
            echo "No se pudo dar de alta el Projecto: " . $projectDTO->getName() . " | " . $e->getMessage();
            return 0;
        }
    }

    public function editProject(ProjectDTO $project)
    {
        // TODO: Implement editProject() method.
    }

    public function deleteProject(int $projectId)
    {
        // TODO: Implement deleteProject() method.
    }
}