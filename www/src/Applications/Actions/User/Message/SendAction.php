<?php

declare(strict_types=1);

namespace App\Application\Actions\User\Message;


use App\Application\Actions\Action;
use App\Domain\User\Message\Message;
use App\Domain\User\Message\MessageRepositoryInterface;
use App\Domain\User\Message\Request\SendRequest;
use Psr\Http\Message\ResponseInterface as Response;

class SendAction extends Action
{

    protected function action(): Response
    {
        /** @var SendRequest $request */
        $request = $this->getDenormalizedRequest(SendRequest::class);

        return $this->respondWithData([
            'id' => $this->get(MessageRepositoryInterface::class)->save(
                new Message(
                    (int)$request->id ?? null,
                    $this->user->getId(),
                    (int)$request->to,
                    (new \DateTime())->format('Y-m-d H:i:s'),
                    $request->content,
                    null,
                    null,
                    null
                )
            )
        ]);
    }
}
