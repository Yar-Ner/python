<?php
declare(strict_types=1);

namespace App\Application\Actions\Vehicle\Containers;

use App\Application\Actions\Action;
use App\Application\Providers\UserProvider;
use App\Domain\SharedContext\BrowserDriver\BrowserSessionFactory;
use App\Domain\User\UserRepository;
use App\Domain\Vehicle\Containers\VehiclesContainersRepository;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

abstract class VehiclesContainersAction extends Action
{
  /**
   * @var VehiclesContainersRepository
   */
  protected $vehiclesContainersRepository;

  public function __construct(
    LoggerInterface $logger,
    VehiclesContainersRepository $vehiclesContainersRepository,
    UserRepository $userRepository,
    UserProvider $userProvider,
    ContainerInterface $container,
    BrowserSessionFactory $browserSessionFactory
  ) {
    parent::__construct($logger, $userRepository, $userProvider, $container, $browserSessionFactory);
    $this->vehiclesContainersRepository = $vehiclesContainersRepository;
  }
}
