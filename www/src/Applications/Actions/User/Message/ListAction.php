<?php

declare(strict_types=1);

namespace App\Application\Actions\User\Message;


use App\Application\Actions\Action;
use App\Domain\User\Message\MessageRepositoryInterface;
use App\Domain\User\Message\Request\ListMessagesRequest;
use App\Domain\User\Message\Response\ListMessagesResponse;
use Psr\Http\Message\ResponseInterface as Response;

class ListAction extends Action
{

    protected function action(): Response
    {
        /** @var ListMessagesRequest $request */
        $request = $this->getDenormalizedRequest(ListMessagesRequest::class);

        return $this->respondWithData(
            new ListMessagesResponse(
                $this->get(MessageRepositoryInterface::class)->getMessages(
                    (int)$request->userId,
                    $request->from,
                    $request->to
                )
            )
        );
    }
}
