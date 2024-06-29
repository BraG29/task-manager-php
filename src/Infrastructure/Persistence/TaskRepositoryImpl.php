<?php

namespace App\Infrastructure\Persistence;

use App\Domain\Repositories\TaskRepository;
use App\Domain\Entities\Task;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use App\Interface\Dtos\TaskDTO;
use Exception;

class TaskRepositoryImpl implements TaskRepository{
    private EntityManager $entityManager;

    /**
     * @var EntityRepository
     */
    private $repository;

    public function __construct(EntityManager $entityManager){

        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository(Task::class);
    }


    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function addTask(Task $task): ?int
    {
        $this->entityManager->persist($task);
        $this->entityManager->flush();
        return $task->getId();
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function updateTask(Task $task): ?int
    {
        $this->entityManager->persist($task);
        $this->entityManager->flush();
        return $task->getId();
    }

    public function deleteTask(Task $task)
    {
        // TODO: Implement deleteTask() method.
    }


    /**
     * <p>Finds a task given the ID</p>
     * @throws OptimisticLockException
     * @throws ORMException
     * @throws Exception
     */
    public function findById(int $id): ?Task
    {
            return $this->entityManager->find(Task::class, $id);
    }


    public function findAll(): array{
        return $this->repository->findAll();
    }

    public function findTasksByProject(int $projectId): ?array
    {
        //$users = $em->getRepository('MyProject\Domain\User')->findBy(array('age' => 20));
        return $this->repository->findBy(array('project' => $projectId));
    }


    public function findTasksByUser($userId): ?array{

        $this->findTasksByUser();
        return $this->repository->findBy(array('project' => $userId));
    }
}