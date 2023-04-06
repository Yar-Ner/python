<?php
declare(strict_types=1);

namespace App\Application\Actions\Monitoring;

use App\Application\Actions\Action;
use App\Application\Providers\UserProvider;
use App\Domain\Monitoring\MonitoringRepository;
use App\Domain\SharedContext\BrowserDriver\BrowserSessionFactory;
use App\Domain\Task\TaskRepository;
use App\Domain\User\UserRepository;
use App\Domain\Vehicle\VehicleRepository;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

abstract class MonitoringAction extends Action
{
    /** @var MonitoringRepository */
    protected $monitoringRepository;

    /** @var TaskRepository */
    protected $taskRepository;

    /** @var VehicleRepository*/
    protected $vehicleRepository;

  public function __construct(
        LoggerInterface $logger,
        MonitoringRepository $monitoringRepository,
        TaskRepository $taskRepository,
        VehicleRepository $vehicleRepository,
        UserRepository $userRepository,
        UserProvider $userProvider,
        ContainerInterface $container,
        BrowserSessionFactory $browserSessionFactory
    ) {
        parent::__construct($logger, $userRepository, $userProvider, $container, $browserSessionFactory);
        $this->monitoringRepository = $monitoringRepository;
        $this->taskRepository = $taskRepository;
        $this->vehicleRepository = $vehicleRepository;
    }
}
