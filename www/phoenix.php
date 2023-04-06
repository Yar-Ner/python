<?php

use App\Application\Settings\SettingsInterface;
use DI\ContainerBuilder;

$settings = require __DIR__ . '/app/settings.php';
$containerBuilder = new ContainerBuilder();

$settings($containerBuilder);

$container = $containerBuilder->build();
$dbConfig = $container->get(SettingsInterface::class)->get('db');

return [
    'migration_dirs' => [
        'first' => __DIR__ . '/migrations',
    ],
    'environments' => [
        'local' => [
            'adapter' => 'mysql',
            'host' => $dbConfig['db_host'],
            'port' => $dbConfig['db_port'],
            'username' => $dbConfig['db_user'],
            'password' => $dbConfig['db_password'],
            'db_name' => $dbConfig['db_schema'],
            'charset' => 'utf8',
        ],
        'production' => [
            'adapter' => 'mysql',
            'host' => $dbConfig['db_host'],
            'port' => $dbConfig['db_port'],
            'username' => $dbConfig['db_user'],
            'password' => $dbConfig['db_password'],
            'db_name' => $dbConfig['db_schema'],
            'charset' => 'utf8',
        ],
    ],
    'default_environment' => 'local',
    'log_table_name' => 'migration_log',
];
