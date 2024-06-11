<?php

namespace App\Domain\Repositories;

use App\Domain\Entities\User;

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

}