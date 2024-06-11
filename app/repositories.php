<?php

use DI\ContainerBuilder;
use App\Domain\Repositories\UserRepository;
use App\Infrastructure\Persistence\UserRepositoryImpl;

return function (ContainerBuilder $containerBuilder){
    $containerBuilder->addDefinitions([
        UserRepository::class => \DI\autowire(UserRepositoryImpl::class)
    ]);
};