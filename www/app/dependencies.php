<?php
declare(strict_types=1);

use App\Application\Settings\SettingsInterface;
use App\Domain\Contractor\DataFetchers\DataFetcher;
use App\Domain\SharedContext\FileUploaderService;
use App\Domain\Task\DataSender\DataSender;
use DI\ContainerBuilder;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Application;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        LoggerInterface::class => function (ContainerInterface $c) {
            $settings = $c->get(SettingsInterface::class);

            $loggerSettings = $settings->get('logger');
            $logger = new Logger($loggerSettings['name']);

            $processor = new UidProcessor();
            $logger->pushProcessor($processor);

            $handler = new StreamHandler($loggerSettings['path'], $loggerSettings['level']);
            $logger->pushHandler($handler);

            return $logger;
        },
        \PDO::class => function (ContainerInterface $container) {
            $db = $container->get(SettingsInterface::class)->get('db');

            $pdo = new PDO(
                sprintf("mysql:host=%s:%s;dbname=%s;charset=UTF8;",$db['db_host'], $db['db_port'], $db['db_schema']),
                $db['db_user'], $db['db_password']);
            $pdo->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);
            $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);

            return $pdo;
        },
        DataFetcher::class => function (ContainerInterface  $container) {
            return new \App\Domain\Contractor\DataFetchers\BitrixApiDataFetcher(
                $container->get(\App\Domain\SharedContext\BrowserDriver\BrowserSessionFactory::class)
            );
        },
        DataSender::class => function (ContainerInterface  $container) {
            return new \App\Domain\Task\DataSender\BitrixApiDataSender(
                $container->get(\App\Domain\SharedContext\BrowserDriver\BrowserSessionFactory::class)
            );
        },
        Application::class => function (ContainerInterface $container) {
            $application = new Application();
            $settings = $container->get(SettingsInterface::class);

            foreach ($settings->get('commands') as $class) {
                $application->add($container->get($class));
            }

            return $application;
        },
        FileUploaderService::class => function (ContainerInterface $container) {
            return new FileUploaderService(
                $container->get(SettingsInterface::class)->get('uploadedPrefix')
            );
        }
    ]);

};
