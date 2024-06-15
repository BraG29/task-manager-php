<?php

namespace App\Domain\Repositories;
use App\Domain\Entities\Task;
use App\Interface\Dtos\TaskDTO;

interface TaskRepository{

    public function createTask(Task $task);

    public function updateTask(Task $task);

    public function deleteTask(Task $task);

    public function findById(int $id): ?Task;

    public function findAll();

    public function findTasksByProject(int $projectId): ?array;
}