<?php
declare(strict_types=1);

namespace App\Application\Actions\User;

use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface as Response;

class LogoutUserAction extends UserAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $this->userTokenRepository->deleteByStringAndUserId(current($this->request->getHeader('token')), $this->user->getId());

        return new \Slim\Psr7\Response();
    }
}
