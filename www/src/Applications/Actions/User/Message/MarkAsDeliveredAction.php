<?php

declare(strict_types=1);

namespace App\Application\Actions\User\Message;


use App\Application\Actions\Action;
use App\Domain\User\Message\MessageRepositoryInterface;
use App\Domain\User\Message\Request\MarkAsDeliveredRequest;
use Psr\Http\Message\ResponseInterface as Response;

class MarkAsDeliveredAction extends Action
{

    protected function action(): Response
    {

//        /** @var MarkAsDeliveredRequest $request */
//        $request = $this->getDenormalizedRequest(MarkAsDeliveredRequest::class);

        $this->get(MessageRepositoryInterface::class)->markAsDelivered($this->request->getParsedBody());

        return $this->respondWithData();
    }
}
