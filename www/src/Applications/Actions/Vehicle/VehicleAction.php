<?php
declare(strict_types=1);

namespace App\Application\Actions\Vehicle;

use App\Application\Actions\Action;
use App\Application\Providers\UserProvider;
use App\Domain\SharedContext\BrowserDriver\BrowserSessionFactory;
use App\Domain\User\UserRepository;
use App\Domain\Vehicle\VehicleRepository;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

abstract class VehicleAction extends Action
{
  /**
   * @var VehicleRepository
   */
  protected $vehicleRepository;

  public function __construct(
    LoggerInterface $logger,
    VehicleRepository $vehicleRepository,
    UserRepository $userRepository,
    UserProvider $userProvider,
    ContainerInterface $container,
    BrowserSessionFactory $browserSessionFactory
  ) {
    parent::__construct($logger, $userRepository, $userProvider, $container, $browserSessionFactory);
    $this->vehicleRepository = $vehicleRepository;
  }
}
