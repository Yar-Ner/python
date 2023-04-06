<?php
declare(strict_types=1);

namespace App\Application\Actions\Chat;

use App\Application\Actions\Action;
use App\Domain\Chat\ChatMessageRepository;
use Psr\Http\Message\ResponseInterface as Response;

class ListUserMessagesAction extends Action
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        /** @var ChatMessageRepository $repository */
        $repository = $this->get(ChatMessageRepository::class);

        return $this->respondWithData(
            $repository->getAllByRecipientIdAndSenderId((int) $this->args['id'], $this->user->getId())
        );
    }
}
