<?php
declare(strict_types=1);

namespace App\Application\Actions\User;

use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface as Response;

class AuthUserAction extends UserAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $body = $this->request->getParsedBody();

        if (is_array($body) && isset($body['login']) && isset($body['password'])) {
            return $this->respondWithData(
                $this->userAuthenticator->auth(
                    $body['login'],
                    $body['password'],
                    $this->request->getAttribute('ip_address')
                )
            );
        }

        return $this->respondWithData([], StatusCodeInterface::STATUS_BAD_REQUEST);
    }
}
