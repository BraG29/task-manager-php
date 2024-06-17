<?php

use App\Interface\ProjectController;
use App\Interface\TaskController;
use DI\ContainerBuilder;
use Slim\Factory\AppFactory;
use Doctrine\ORM\EntityManager;
use App\Interface\UserController;

require __DIR__ . '/../vendor/autoload.php';

$containerBuilder = new ContainerBuilder();

$doctrine = require __DIR__ . '/../app/doctrine.php';
$containerBuilder->addDefinitions([ EntityManager::class => $doctrine() ]);

$repositories = require __DIR__ . '/../app/repositories.php';
$repositories($containerBuilder);

$controllers = require __DIR__ . '/../app/controllers.php';
$controllers($containerBuilder);

$container = $containerBuilder->build();

AppFactory::setContainer($container);
$app = AppFactory::create();

$routesUser = require __DIR__ . '/../app/routesUser.php';
$routesUser($app, $container->get(UserController::class));

$routesProject = require __DIR__ . '/../app/routesProject.php';
$routesProject($app, $container->get(ProjectController::class));

$routesTask = require __DIR__ . '/../app/routesTask.php';
$routesTask($app, $container->get(TaskController::class));

$app->run();
