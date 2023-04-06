<?php
declare(strict_types=1);

namespace App\Application\Actions\Vehicle\Types;

use App\Application\Actions\Action;
use App\Application\Providers\UserProvider;
use App\Domain\SharedContext\BrowserDriver\BrowserSessionFactory;
use App\Domain\User\UserRepository;
use App\Domain\Vehicle\Types\VehiclesTypesRepository;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

abstract class VehiclesTypesAction extends Action
{
  /**
   * @var VehiclesTypesRepository
   */
  protected $vehiclesTypesRepository;

  public function __construct(
    LoggerInterface $logger,
    VehiclesTypesRepository $vehiclesTypesRepository,
    UserRepository $userRepository,
    UserProvider $userProvider,
    ContainerInterface $container,
    BrowserSessionFactory $browserSessionFactory
  ) {
    parent::__construct($logger, $userRepository, $userProvider, $container, $browserSessionFactory);
    $this->vehiclesTypesRepository = $vehiclesTypesRepository;
  }
}
