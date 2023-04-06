<?php
declare(strict_types=1);

namespace App\Application\Actions\Geo;

use App\Application\Actions\Action;
use App\Application\Providers\UserProvider;
use App\Domain\Geo\GeoRepository;
use App\Domain\SharedContext\BrowserDriver\BrowserSessionFactory;
use App\Domain\User\UserRepository;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

abstract class GeoAction extends Action
{
    /** @var GeoRepository */
    protected $geoRepository;

    public function __construct(
        LoggerInterface $logger,
        GeoRepository $geoRepository,
        UserRepository $userRepository,
        UserProvider $userProvider,
        ContainerInterface $container,
        BrowserSessionFactory $browserSessionFactory
    ) {
        parent::__construct($logger, $userRepository, $userProvider, $container, $browserSessionFactory);
        $this->geoRepository = $geoRepository;
    }
}
