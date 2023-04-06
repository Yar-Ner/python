<?php
declare(strict_types=1);

namespace App\Application\Actions\User\Rule;

use App\Application\Actions\Action;
use App\Application\Providers\UserProvider;
use App\Domain\SharedContext\BrowserDriver\BrowserSessionFactory;
use App\Domain\User\Rule\RuleRepository;
use App\Domain\User\UserRepository;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

abstract class RuleAction extends Action
{
    /** @var RuleRepository */
    protected $ruleRepository;

    public function __construct(
        LoggerInterface $logger,
        RuleRepository $ruleRepository,
        UserRepository $userRepository,
        UserProvider $userProvider,
        ContainerInterface $container,
        BrowserSessionFactory $browserSessionFactory
    ) {
        parent::__construct($logger, $userRepository, $userProvider, $container, $browserSessionFactory);
        $this->ruleRepository = $ruleRepository;
    }
}
