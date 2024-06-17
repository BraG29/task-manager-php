<?php

namespace App\Interface;

use App\Domain\Entities\Task;
use App\Interface\Dtos\TaskDTO;

interface TaskController{

    public function getTasksByUser(int $userId): ?array;

    public function getTasksByProject(int $projectId): array;

    public function getTaskById(int $taskId): ?TaskDTO;

    public function createTask(TaskDTO $taskDTO): ?int;

    public function updateTask(Task $taskId);

    public function deleteTask(int $taskId);
}