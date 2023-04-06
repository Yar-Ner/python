<?php

declare(strict_types=1);

namespace App\Application\Actions\User\Message;


use App\Application\Actions\Action;
use App\Domain\User\Message\MessageRepositoryInterface;
use App\Domain\User\Message\Request\MarkAsReadRequest;
use Psr\Http\Message\ResponseInterface as Response;

class MarkAsReadAction extends Action
{

    protected function action(): Response
    {
//        /** @var MarkAsReadRequest $request */
//        $request = $this->getDenormalizedRequest(MarkAsReadRequest::class);

        $this->get(MessageRepositoryInterface::class)->markAsRead($this->request->getParsedBody());

        return $this->respondWithData();
    }
}
