<?php
declare(strict_types=1);

namespace App\Application\Actions\User\Group;


use App\Application\Actions\Action;
use App\Application\Actions\ActionPayload;
use App\Domain\User\Group\GroupRepository;
use App\Domain\User\Rule\Request\CreateGroupRequest;
use App\Domain\User\Rule\Rule;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface as Response;

class ViewGroupAction extends Action
{

    protected function action(): Response
    {
        $id = (int) $this->request->getAttribute('id');

        /** @var GroupRepository $repository */
        $repository = $this->get(GroupRepository::class);

        $rule = $repository->getById($id);

        return $this->respondWithData($rule);
    }
}