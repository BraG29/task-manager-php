<?php

use App\Application\Controllers\ProjectControllerImpl;
use App\Interface\ProjectController;
use App\Interface\UserController;
use App\Application\Controllers\UserControllerImpl;
use DI\ContainerBuilder;
use App\Application\Controllers\TaskControllerImpl;
use App\Interface\TaskController;


return function (ContainerBuilder $containerBuilder){
    $containerBuilder->addDefinitions([
        UserController::class => DI\autowire(UserControllerImpl::class),
        ProjectController::class => DI\autowire(ProjectControllerImpl::class),
        TaskController::class => DI\autowire(TaskControllerImpl::class)
    ]);
};