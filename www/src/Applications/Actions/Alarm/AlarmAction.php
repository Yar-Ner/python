<?php
declare(strict_types=1);

namespace App\Application\Actions\Alarm;

use App\Application\Actions\Action;
use App\Application\Providers\UserProvider;
use App\Domain\Alarm\AlarmRepository;
use App\Domain\SharedContext\BrowserDriver\BrowserSessionFactory;
use App\Domain\User\UserRepository;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

abstract class AlarmAction extends Action
{
    /** @var AlarmRepository */
    protected $alarmRepository;

    public function __construct(
        LoggerInterface $logger,
        AlarmRepository $alarmRepository,
        UserRepository $userRepository,
        UserProvider $userProvider,
        ContainerInterface $container,
        BrowserSessionFactory $browserSessionFactory
    ) {
        parent::__construct($logger, $userRepository, $userProvider, $container, $browserSessionFactory);
        $this->alarmRepository = $alarmRepository;
    }
}
