<?php

namespace App\Infrastructure\Persistence;

use App\Domain\Entities\Creatable;
use App\Domain\Entities\Project;
use App\Domain\Repositories\UserRepository;
use App\Domain\Entities\User;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

class UserRepositoryImpl implements UserRepository
{
    private EntityManager $entityManager;

    /**
     * @var EntityRepository
     */
    private EntityRepository $repository;

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository(User::class);

    }

    public function findById(int $id): ?User
    {
        return $this->entityManager->find(User::class, $id);
    }

    public function findAll(): array
    {
        return $this->repository->findAll();
    }

    public function save(User $user): void
    {
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    public function findByEmail(string $email): ?User
    {
        return $this->entityManager->find(User::class, $email);
    }

}