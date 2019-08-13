<?php

use DI\ContainerBuilder;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

$containerBuilder = new ContainerBuilder;

// This should be enabled in production
if (false) {
    $containerBuilder->enableCompilation(__DIR__ . '/../var/cache');
}

// Setup dependencies
$dependencies = require __DIR__ . '/dependencies.php';
$containerBuilder->addDefinitions($dependencies);

// Build Container
$container = $containerBuilder->build();

AppFactory::setContainer($container);
$app = AppFactory::create();
$callableResolve = $app->getCallableResolver();

// Register middleware
$middlewares = require __DIR__ . '/../app/middleware.php';
foreach ($middlewares as $middleware) {
    $app->add($middleware);
}