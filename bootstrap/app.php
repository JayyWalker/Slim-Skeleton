<?php

require __DIR__ . '/../vendor/autoload.php';

$containerBuilder = new \DI\ContainerBuilder;

// This should be enabled in production
if (false) {
    $containerBuilder->enableCompilation(__DIR__ . '/../var/cache');
}

// Setup dependencies
$dependencies = require __DIR__ . '/dependencies.php';
$containerBuilder->addDefinitions($dependencies);

// Build Container
$container = $containerBuilder->build();

\Slim\Factory\AppFactory::setContainer($container);
$app = \Slim\Factory\AppFactory::create();
$callableResolve = $app->getCallableResolver();


