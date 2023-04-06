<?php
declare(strict_types=1);

namespace App\Application\Actions\Chat;

use App\Application\Actions\Action;
use App\Domain\Chat\UserAdapter;
use App\Domain\User\User;
use App\Domain\User\UserRepository;
use Psr\Http\Message\ResponseInterface as Response;

class ListAllUsersAction extends Action
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        return $this->respondWithData(
            array_map(
                static function (User $user) {
                    return UserAdapter::toChatFormat($user);
                },
                $this->get(UserRepository::class)->findAll()
            )
        );
    }
}
