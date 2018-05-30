<?php

use Doctrine\ORM\Tools\Console\ConsoleRunner;

// replace with file to your own project bootstrap
require_once __DIR__ . '/vendor/autoload.php';

Amp\Loop::run(Amp\GreenThread\coroutine(function () {
    $entityManager = require __DIR__ . '/bootstrap.php';
    $helperSet = ConsoleRunner::createHelperSet($entityManager);

    $commands = [];

    ConsoleRunner::run($helperSet, $commands);
}));
