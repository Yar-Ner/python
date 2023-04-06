<?php
declare(strict_types=1);

namespace App\Application\Actions\Chat;

use App\Application\Actions\Action;
use App\Domain\Chat\ChatMessageRepository;
use App\Domain\Chat\UserAdapter;
use App\Domain\User\User;
use App\Domain\User\UserRepository;
use Psr\Http\Message\ResponseInterface as Response;

class ListAllChatsAction extends Action
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $currentUserId = $this->user->getId();

        /** @var ChatMessageRepository $chatMessagesRepository */
        $chatMessagesRepository = $this->get(ChatMessageRepository::class);

        return $this->respondWithData(
            array_map(
                static function (User $user) use ($currentUserId, $chatMessagesRepository) {
                    return UserAdapter::toChatFormat(
                        $user,
                        $currentUserId,
                        $chatMessagesRepository->getUnreadCount(
                            $currentUserId,
                            $user->getId()
                        ),
                        $chatMessagesRepository->getLastMessage(
                          $currentUserId,
                          $user->getId()
                        )
                    );
                },
                $this->get(UserRepository::class)->findAll()
            )
        );
    }
}
