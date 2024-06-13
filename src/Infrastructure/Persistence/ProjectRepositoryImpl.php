<?php

namespace App\Infrastructure\Persistence;

use App\Domain\Repositories\ProjectRepository;
use App\Domain\Entities\Project;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

class ProjectRepositoryImpl implements ProjectRepository{

    private EntityManager $entityManager;

    /**
     * @var EntityRepository
     */
    private $repository;

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository(Project::class);

    }

    public function createProject()
    {
        // TODO: Implement createProject() method.
    }

    public function editProject()
    {
        // TODO: Implement editProject() method.
    }

    public function deleteProject()
    {
        // TODO: Implement deleteProject() method.
    }

    public function findById()
    {
        // TODO: Implement findById() method.
    }

    public function findAll()
    {
        // TODO: Implement findAll() method.
    }
}