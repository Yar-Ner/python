<?php
declare(strict_types=1);

namespace App\Application\Actions\Chat;

use App\Application\Actions\Action;
use App\Domain\Chat\ChatMainState;
use App\Domain\Chat\ChatMessageRepository;
use App\Domain\Chat\UserAdapter;
use App\Domain\User\User;
use App\Domain\User\UserRepository;
use Psr\Http\Message\ResponseInterface as Response;

class ResetChatCounterAction extends Action
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        /** @var ChatMessageRepository $chatMessagesRepository */
        $chatMessagesRepository = $this->get(ChatMessageRepository::class);

        $chatMessagesRepository->resetUnreadCount($this->user->getId(), (int) $this->args['id']);

        return $this->respondWithData();
    }
}
