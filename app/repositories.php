<?php

use DI\ContainerBuilder;
use App\Infrastructure\Persistence\TaskRepositoryImpl;
use App\Infrastructure\Persistence\UserRepositoryImpl;
use App\Domain\Repositories\UserRepository;
use App\Domain\Repositories\TaskRepository;

return function (ContainerBuilder $containerBuilder){
    $containerBuilder->addDefinitions([
        UserRepository::class => \DI\autowire(UserRepositoryImpl::class)
    ]);

    $containerBuilder->addDefinitions([
        TaskRepository::class => \DI\autowire(TaskRepositoryImpl::class)
    ]);

};
