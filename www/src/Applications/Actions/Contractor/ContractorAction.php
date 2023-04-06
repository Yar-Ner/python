<?php
declare(strict_types=1);

namespace App\Application\Actions\Contractor;

use App\Application\Actions\Action;
use App\Application\Providers\UserProvider;
use App\Domain\Contractor\ContractorRepository;
use App\Domain\SharedContext\BrowserDriver\BrowserSessionFactory;
use App\Domain\User\UserRepository;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

abstract class ContractorAction extends Action
{
    /** @var ContractorRepository */
    protected $contractorRepository;

    public function __construct(
        LoggerInterface $logger,
        ContractorRepository $contractorRepository,
        UserRepository $userRepository,
        UserProvider $userProvider,
        ContainerInterface $container,
        BrowserSessionFactory $browserSessionFactory
    ) {
        parent::__construct($logger, $userRepository, $userProvider, $container, $browserSessionFactory);
        $this->contractorRepository = $contractorRepository;
    }
}
