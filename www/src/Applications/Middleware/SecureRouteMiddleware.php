<?php

namespace App\Application\Middleware;

use App\Application\Providers\RoleProvider;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Interfaces\RouteInterface;
use Slim\Routing\RouteContext;
use function PHPUnit\Framework\returnArgument;


class SecureRouteMiddleware
{

    private $container;
    /**
     * @var RoleProvider
     */
    private $roleProvider;

    public function __construct(RoleProvider $roleProvider)
    {
        $this->roleProvider = $roleProvider;
    }

    /**
     * @param ServerRequestInterface $request
     * @param RequestHandler $handler
     * @return ResponseInterface
     */
    public function __invoke(Request $request, RequestHandler  $handler)
    {
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();

        if (!$route instanceof RouteInterface) {
            return $handler->handle($request);
        }

        $roles = $this->roleProvider->getRoles($request);

        $allowedRoles = $request->getAttribute('allowedRoles') ?? [];
        $allowed = true;

        if (is_array($allowedRoles) && $allowedRoles) {
            $allowed = false;
            if (RoleProvider::isHaveAllowedRole($roles, $allowedRoles)) {
                $allowed = true;
            }
        }

        if ($allowed === false) {
            return new \Slim\Psr7\Response(StatusCodeInterface::STATUS_FORBIDDEN);
        }

        return $handler->handle($request);
    }
}
