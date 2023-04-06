<?php

namespace App\Application\Providers;

use App\Domain\User\Rule\RuleRepository;
use App\Infrastructure\Persistence\User\Rule\MySQLRuleRepository;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Tkhamez\Slim\RoleAuth\RoleProviderInterface;

class RoleProvider implements RoleProviderInterface
{
    private $container;

    /**
     * @var UserProvider
     */
    private $userProvider;

    public function __construct(ContainerInterface $container, UserProvider $userProvider)
    {
        $this->container = $container;
        $this->userProvider = $userProvider;
    }

    public function getRoles(ServerRequestInterface $request): array
    {
        $result = ['guest'];
        $user = $this->userProvider->getUser($request);

        if ($user !== null) {
            $userRoles = $this->container->get(RuleRepository::class)->getHandleByIds($user->getRulesId());
            $userRoles[] = 'user';
            $result = $userRoles;
        }

        return $result;
  }

    public static function isHaveAllowedRole(array $roles, array $allowedRoles)
    {
        foreach ($roles as $role) {
            if (in_array($role, $allowedRoles, true)) {
                return TRUE;
            }
        }

        return FALSE;
    }
}
