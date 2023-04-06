<?php
declare(strict_types=1);

use App\Application\Commands\Contractor\FetchBitrixContactorsCommand;
use App\Application\Commands\Task\SendBitrixTasksDistanceCommand;
use App\Application\Settings\Settings;
use App\Application\Settings\SettingsInterface;
use DI\ContainerBuilder;
use Monolog\Logger;

return function (ContainerBuilder $containerBuilder) {

    // Global Settings Object
    $containerBuilder->addDefinitions([
        SettingsInterface::class => function () {
            return new Settings([
                'displayErrorDetails' => true, // Should be set to false in production
                'logError'            => false,
                'logErrorDetails'     => false,
                'templates'           => __DIR__.'/../templates/',
                'uploadedPrefix'      => __DIR__.'/../public/files',
                'logger' => [
                    'name' => 'slim-app',
                    'path' => isset($_ENV['docker']) ? 'php://stdout' : __DIR__ . '/../logs/app.log',
                    'level' => Logger::DEBUG,
                ],
                'db' => require __DIR__ .'/db.php',
                'commands' => [
                    FetchBitrixContactorsCommand::class,
                    SendBitrixTasksDistanceCommand::class
                ],
            ]);
        }
    ]);
};
