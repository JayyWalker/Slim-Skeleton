<?php
declare(strict_types=1);

use App\Domain\User\UserRepository;
use App\Infrastructure\Persistence\User\InMemoryUserRepository;
use function DI\autowire;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

return [
    LoggerInterface::class => function (ContainerInterface $c) {
        $config = $c->get('config')['logger'];

        $logger = new Logger($config['name']);

        $processor = new UidProcessor();
        $logger->pushProcessor($processor);

        $handler = new StreamHandler($config['path'], $config['level']);
        $logger->pushHandler($handler);

        return $logger;
    },

    // Repositories
    UserRepository::class => autowire(InMemoryUserRepository::class),
];
