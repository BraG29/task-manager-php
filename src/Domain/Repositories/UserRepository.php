<?php

namespace App\Domain\Repositories;

use App\Domain\Entities\User;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;

interface UserRepository
{
    /**
     * @param int $id
     * @return User | null
     */
    public function findById(int $id): ?User;

    /**
     * @return User[]
     */
    public function findAll(): array;


    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function save(User $user): void;

}