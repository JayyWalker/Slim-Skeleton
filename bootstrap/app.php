<?php

use App\Application\Handlers\HttpErrorHandler;
use App\Application\Handlers\ShutdownHandler;
use App\Application\ResponseEmitter\ResponseEmitter;
use DI\ContainerBuilder;
use Slim\Factory\AppFactory;
use Slim\Factory\ServerRequestCreatorFactory;

require __DIR__ . '/../vendor/autoload.php';

// Load env variables
$dotenv = Dotenv\Dotenv::create(__DIR__ . '/../');
$dotenv->load();

$containerBuilder = new ContainerBuilder;

// This should be enabled in production
if (false) {
    $containerBuilder->enableCompilation(__DIR__ . '/../storage/cache');
}

// Setup dependencies
$dependencies = require __DIR__ . '/dependencies.php';
$containerBuilder->addDefinitions($dependencies);

// Set up settings
$configLoader = require __DIR__ . '/../config/config-loader.php';
$config = $configLoader();

$containerBuilder->addDefinitions($config);

// Build Container
$container = $containerBuilder->build();

AppFactory::setContainer($container);
$app = AppFactory::create();
$callableResolver = $app->getCallableResolver();

// Register middleware
$middlewareArray = require __DIR__ . '/../app/middleware.php';
foreach ($middlewareArray as $middleware) {
    $app->add($middleware);
}

$routes = require __DIR__ . '/../app/routes.php';
$routes($app);

/** @var bool $displayErrorDetails */
$displayErrorDetails = $container->get('settings')['displayErrorDetails'];

// Create Request object from globals
$serverRequestCreator = ServerRequestCreatorFactory::create();
$request = $serverRequestCreator->createServerRequestFromGlobals();

// Create Error Handler
$responseFactory = $app->getResponseFactory();
$errorHandler = new HttpErrorHandler($callableResolver, $responseFactory);

// Create Shutdown Handler
$shutdownHandler = new ShutdownHandler($request, $errorHandler, $displayErrorDetails);
register_shutdown_function($shutdownHandler);

// Add Routing Middleware
$app->addRoutingMiddleware();

// Add Error Middleware
$errorMiddleware = $app->addErrorMiddleware($displayErrorDetails, false, false);
$errorMiddleware->setDefaultErrorHandler($errorHandler);

// Run App & Emit Response
$response = $app->handle($request);
$responseEmitter = new ResponseEmitter();
$responseEmitter->emit($response);
