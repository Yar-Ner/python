<?php
declare(strict_types=1);

namespace App\Application\Actions\Settings;

use App\Application\Actions\Action;
use App\Application\Providers\UserProvider;
use App\Domain\Settings\DefaultSettingsRepository;
use App\Domain\SharedContext\BrowserDriver\BrowserSessionFactory;
use App\Domain\User\UserRepository;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

abstract class SettingsAction extends Action
{
    /**
     * @var DefaultSettingsRepository
     */
    protected $defaultSettingsRepository;

    public function __construct(
        LoggerInterface $logger,
        DefaultSettingsRepository $defaultSettingsRepository,
        UserRepository $userRepository,
        UserProvider $userProvider,
        ContainerInterface $container,
        BrowserSessionFactory $browserSessionFactory
    ) {
        parent::__construct($logger, $userRepository, $userProvider, $container, $browserSessionFactory);
        $this->defaultSettingsRepository = $defaultSettingsRepository;
    }
}
