<?php
declare(strict_types=1);

namespace App\Application\Actions\Photo;

use App\Application\Actions\Action;
use App\Application\Providers\UserProvider;
use \App\Domain\Photo\PhotoRepositoryInterface;
use App\Domain\SharedContext\BrowserDriver\BrowserSessionFactory;
use App\Domain\User\UserRepository;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

abstract class PhotoAction extends Action
{
  /**
   * @var PhotoRepositoryInterface
   */
  protected $photoRepositoryInterface;

  public function __construct(
    LoggerInterface $logger,
    PhotoRepositoryInterface $photoRepositoryInterface,
    UserRepository $userRepository,
    UserProvider $userProvider,
    ContainerInterface $container,
    BrowserSessionFactory $browserSessionFactory
  ) {
    parent::__construct($logger, $userRepository, $userProvider, $container, $browserSessionFactory);
    $this->photoRepositoryInterface = $photoRepositoryInterface;
  }
}
