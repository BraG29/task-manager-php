<?php

namespace App\Domain\Repositories;

//use App\Domain\Entities\Project;

use App\Domain\Entities\Project;
use App\Infrastructure\Persistence\ProjectRepositoryImpl;

interface ProjectRepository
{

    public function createProject(Project $project): int;
    public  function editProject();
    public  function deleteProject();
    public function findById(int $id):?Project;
    public function findAll();

}