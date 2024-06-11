<?php

use App\Interface\UserController;
use App\Application\Controllers\UserControllerImpl;
use DI\ContainerBuilder;

return function (ContainerBuilder $containerBuilder){
    $containerBuilder->addDefinitions([
        UserController::class => \DI\autowire(UserControllerImpl::class)
    ]);
};