<?php
declare(strict_types=1);

namespace App\Application\Actions\User\Group;

use App\Application\Actions\Action;
use App\Application\Providers\UserProvider;
use App\Domain\SharedContext\BrowserDriver\BrowserSessionFactory;
use App\Domain\User\Group\GroupRepository;
use App\Domain\User\UserRepository;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

abstract class GroupAction extends Action
{
    /** @var GroupRepository */
    protected $groupRepository;

    public function __construct(
        LoggerInterface $logger,
        GroupRepository $groupRepository,
        UserRepository $userRepository,
        UserProvider $userProvider,
        ContainerInterface $container,
        BrowserSessionFactory $browserSessionFactory
    ) {
        parent::__construct($logger, $userRepository, $userProvider, $container, $browserSessionFactory);
        $this->groupRepository = $groupRepository;
    }
}
