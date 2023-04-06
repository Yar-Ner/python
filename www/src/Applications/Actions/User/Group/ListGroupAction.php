<?php
declare(strict_types=1);

namespace App\Application\Actions\User\Group;


use App\Application\Actions\Action;
use App\Domain\User\Group\GroupRepository;
use Psr\Http\Message\ResponseInterface as Response;

class ListGroupAction extends Action
{

    protected function action(): Response
    {
        /** @var GroupRepository $repository */
        $repository = $this->get(GroupRepository::class);

        return $this->respondWithData($repository->findAll());
    }
}