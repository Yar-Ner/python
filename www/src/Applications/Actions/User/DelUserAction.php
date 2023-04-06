<?php
declare(strict_types=1);

namespace App\Application\Actions\User;

use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface as Response;

class DelUserAction extends UserAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $id = (int) $this->request->getAttribute('id');

        $this->userRepository->delete($id);

        return new \Slim\Psr7\Response(StatusCodeInterface::STATUS_OK);
    }
}