<?php

namespace App\Domain\Repositories;

//use App\Domain\Entities\Project;

interface ProjectRepository
{

    public  function createProject();
    public  function editProject();
    public  function deleteProject();
    public function findById();
    public function findAll();

}