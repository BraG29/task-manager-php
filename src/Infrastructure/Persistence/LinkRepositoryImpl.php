<?php

namespace App\Infrastructure\Persistence;

use App\Domain\Entities\Link;
use App\Domain\Repositories\LinkRepository;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

class LinkRepositoryImpl implements LinkRepository{

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
        $this->repository = $entityManager->getRepository(Link::class);

    }

    public function save(Link $link): void
    {
        $this->entityManager->persist($link);
        $this->entityManager->flush();
    }
}