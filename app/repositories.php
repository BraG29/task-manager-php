<?php

use DI\ContainerBuilder;
use App\Infrastructure\Persistence\TaskRepositoryImpl;
use App\Infrastructure\Persistence\UserRepositoryImpl;
use \App\Infrastructure\Persistence\ProjectRepositoryImpl;
use App\Domain\Repositories\UserRepository;
use App\Domain\Repositories\TaskRepository;
use \App\Domain\Repositories\ProjectRepository;

return function (ContainerBuilder $containerBuilder){
    $containerBuilder->addDefinitions([
        UserRepository::class => \DI\autowire(UserRepositoryImpl::class),
        ProjectRepository::class => \DI\autowire(ProjectRepositoryImpl::class),
        TaskRepository::class => \DI\autowire(TaskRepositoryImpl::class)
    ]);

};
