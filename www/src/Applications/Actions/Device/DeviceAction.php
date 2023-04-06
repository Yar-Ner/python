<?php
declare(strict_types=1);

namespace App\Application\Actions\Device;

use App\Application\Actions\Action;
use App\Application\Providers\UserProvider;
use App\Domain\Device\DeviceRepository;
use App\Domain\SharedContext\BrowserDriver\BrowserSessionFactory;
use App\Domain\User\UserRepository;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

abstract class DeviceAction extends Action
{
    /** @var DeviceRepository */
    protected $deviceRepository;

    public function __construct(
        LoggerInterface $logger,
        DeviceRepository $deviceRepository,
        UserRepository $userRepository,
        UserProvider $userProvider,
        ContainerInterface $container,
      BrowserSessionFactory $browserSessionFactory
    ) {
        parent::__construct($logger, $userRepository, $userProvider, $container, $browserSessionFactory);
        $this->deviceRepository = $deviceRepository;
    }
}
