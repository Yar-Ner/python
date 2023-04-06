<?php

namespace App\Application\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class SetRoles
{

    private $roles;

    public function __construct($roles)
    {
        $this->roles = $roles;
    }

    /**
     * @param Request $request
     * @param RequestHandler $handler
     * @return Response
     */
    public function __invoke(Request $request, RequestHandler  $handler)
    {
        $request = $request->withAttribute('allowedRoles', $this->roles);

        return $handler->handle($request);
    }
}
