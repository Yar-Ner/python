<?php
declare(strict_types=1);

namespace App\Application\Actions\Chat;

use App\Application\Actions\Action;
use App\Domain\Chat\ChatMainState;
use App\Domain\Chat\UserAdapter;
use App\Domain\User\User;
use App\Domain\User\UserRepository;
use Psr\Http\Message\ResponseInterface as Response;

class ListChatMainStateAction extends Action
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        return $this->respondWithData(
            new ChatMainState(
                array_map(
                    function (User $user) {
                        return UserAdapter::toChatFormat($user, $this->user->getId());
                    },
                    $this->get(UserRepository::class)->findAll()
                ),
                $this->user->getId()
            )
        );
    }
}
