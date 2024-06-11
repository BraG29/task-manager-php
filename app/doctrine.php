<?php

use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;

require_once __DIR__ . '/../vendor/autoload.php';

return function () {
    $config = ORMSetup::createAttributeMetadataConfiguration(
        paths: [__DIR__ . "/../src/Domain/Entities"],
        isDevMode: true,
    );

    $connection = DriverManager::getConnection([
        'driver' => 'pdo_mysql',
        'host' => 'localhost',
        'user' => 'admin',
        'password' => 'sanguja4',
        'dbname' => 'task_manager_db',
        'charset' => 'utf8mb4',
    ]);

    return new EntityManager($connection, $config);
};
