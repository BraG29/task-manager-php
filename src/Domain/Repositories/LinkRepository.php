<?php

namespace App\Domain\Repositories;


use App\Domain\Entities\Link;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;

interface LinkRepository{

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function save(Link $link): void;

}