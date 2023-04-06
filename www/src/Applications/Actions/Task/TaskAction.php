<?php
declare(strict_types=1);

namespace App\Application\Actions\Task;

use App\Application\Actions\Action;
use App\Application\Providers\UserProvider;
use App\Domain\Monitoring\MonitoringRepository;
use App\Domain\SharedContext\BrowserDriver\BrowserSessionFactory;
use App\Domain\Task\TaskRepository;
use App\Domain\User\UserRepository;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

abstract class TaskAction extends Action
{
    /** @var TaskRepository */
    protected $taskRepository;

    /** @var MonitoringRepository */
    protected $monitoringRepository;

    public function __construct(
        LoggerInterface $logger,
        TaskRepository $taskRepository,
        MonitoringRepository $monitoringRepository,
        UserRepository $userRepository,
        UserProvider $userProvider,
        ContainerInterface $container,
        BrowserSessionFactory $browserSessionFactory
    ) {
        parent::__construct($logger, $userRepository, $userProvider, $container, $browserSessionFactory);
        $this->taskRepository = $taskRepository;
        $this->monitoringRepository = $monitoringRepository;
    }
}
