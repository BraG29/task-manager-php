<?php



use App\Interface\ProjectController;
use App\Domain\Repositories\ProjectRepository;
use App\Interface\Dtos\ProjectDTO;
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

    public function createProject(ProjectDTO $project)
    {
        // TODO: Implement createProject() method.
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