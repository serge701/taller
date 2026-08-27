<?php
declare(strict_types=1);

return [
    'db' => [
        'host'    => $_ENV['DB_HOST'] ?? '127.0.0.1',
        'port'    => $_ENV['DB_PORT'] ?? '3306',
        'name'    => $_ENV['DB_NAME'] ?? 'taller',
        'user'    => $_ENV['DB_USER'] ?? 'root',
        'pass'    => $_ENV['DB_PASS'] ?? '',
        'charset' => 'utf8mb4',
    ],

    'app' => [
        'name'     => 'Taller Mecánico Reyes',
        'env'      => $_ENV['APP_ENV']      ?? 'local',
        'debug'    => filter_var($_ENV['APP_DEBUG'] ?? 'true', FILTER_VALIDATE_BOOLEAN),
        'timezone' => $_ENV['APP_TIMEZONE'] ?? 'America/Mexico_City',
    ],

    'session' => [
        'name'     => 'taller_session',
        'lifetime' => 60 * 60 * 9,
    ],
];
