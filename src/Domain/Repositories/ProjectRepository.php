<?php

namespace App\Domain\Repositories;

use App\Domain\Entities\Project;

interface ProjectRepository
{
    public function createProject(Project $project): int;
    public  function editProject(Project $project): int;
    public  function deleteProject(int $projectId);
    public function findById(int $id):?Project;
    public function findAll();

}