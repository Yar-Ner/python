<?php
declare(strict_types=1);

namespace App\Application\Actions\User;

use App\Application\Actions\Action;
use App\Application\Providers\UserProvider;
use App\Application\UserAuthenticator;
use App\Domain\Device\DeviceRepository;
use App\Domain\Settings\DefaultSettingsRepository;
use App\Domain\SharedContext\BrowserDriver\BrowserSessionFactory;
use App\Domain\User\Rule\RuleRepository;
use App\Domain\User\UserRepository;
use App\Domain\User\UserTokenRepository;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

abstract class UserAction extends Action
{
    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * @var UserAuthenticator
     */

    protected $userAuthenticator;
    /**
     * @var UserProvider
     */

    protected $userProvider;
    /**
     * @var RuleRepository
     */
    protected $ruleRepository;

    /**
     * @var UserTokenRepository
     */
    protected $userTokenRepository;

    /**
     * @var DeviceRepository
     */
    protected $deviceRepository;

    /**
     * @var DefaultSettingsRepository
     */
    protected $defaultSettingsRepository;

    public function __construct(
        LoggerInterface $logger,
        UserRepository $userRepository,
        UserAuthenticator $userAuthenticator,
        UserProvider $userProvider,
        RuleRepository $ruleRepository,
        UserTokenRepository $userTokenRepository,
        DeviceRepository $deviceRepository,
        DefaultSettingsRepository $defaultSettingsRepository,
        ContainerInterface $container,
        BrowserSessionFactory $browserSessionFactory
    ) {
        parent::__construct($logger, $userRepository, $userProvider, $container, $browserSessionFactory);

        $this->userRepository = $userRepository;
        $this->logger = $logger;
        $this->userAuthenticator = $userAuthenticator;
        $this->userProvider = $userProvider;
        $this->ruleRepository = $ruleRepository;
        $this->userTokenRepository = $userTokenRepository;
        $this->deviceRepository = $deviceRepository;
        $this->defaultSettingsRepository = $defaultSettingsRepository;
    }
}
