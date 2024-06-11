<?php

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

$routes = require __DIR__ . '/../app/routes.php';
$routes($app, $container->get(UserController::class));

$app->run();
