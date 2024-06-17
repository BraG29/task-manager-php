<?php

use DI\ContainerBuilder;
use App\Application\Controllers\TaskControllerImpl;
use App\Application\Controllers\UserControllerImpl;
use App\Interface\TaskController;
use App\Interface\UserController;


return function (ContainerBuilder $containerBuilder){
    $containerBuilder->addDefinitions([
        UserController::class => \DI\autowire(UserControllerImpl::class)
    ]);
    $containerBuilder->addDefinitions([
        TaskController::class => \DI\autowire(TaskControllerImpl::class)
    ]);
};