<?php

use Monolog\Logger;

return [
    'logger' => [
        'name' => getenv('APP_NAME') ?? 'slim-app',
        'path' => isset($_ENV['docker']) ? 'php://stdout' : __DIR__ . '/../storage/logs/app.log',
        'level' => Logger::DEBUG,
    ],
];
