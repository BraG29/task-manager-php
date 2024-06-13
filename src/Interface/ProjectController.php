<?php

namespace App\Interface;

use App\Interface\Dtos\ProjectDTO;


interface ProjectController
{

    public function getProjectData(int $projectId);

    public function getProjectDataByUser(int $userId);

    public function createProject(ProjectDTO $project);

    public function editProject(ProjectDTO $project);

    public function deleteProject(int $projectId);

}