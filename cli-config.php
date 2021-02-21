<?php

// cli-config.php

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Slim\App;

/** @var App $app */
$app = require_once __DIR__ . '/public/boostrap.php';

return ConsoleRunner::createHelperSet($app->getContainer()->get(EntityManager::class));

