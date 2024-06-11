<?php

use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Doctrine\ORM\Tools\Console\EntityManagerProvider\SingleManagerProvider;

$doctrine = require __DIR__ . '/../app/doctrine.php';

ConsoleRunner::run(
    new SingleManagerProvider($doctrine())
);
