<?php
declare(strict_types=1);

namespace App\Application\Actions\User\Group;


use App\Application\Actions\Action;
use App\Domain\User\Group\Group;
use App\Domain\User\Group\GroupRepository;
use App\Domain\User\Group\Request\CreateGroupRequest;
use App\Domain\User\Group\Request\UpdateGroupRequest;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface as Response;

class SaveGroupAction extends Action
{

    protected function action(): Response
    {
        $id = (int) $this->request->getAttribute('id');

        /** @var GroupRepository $repository */
        $repository = $this->get(GroupRepository::class);

        if ($id) {
            $group = $repository->getById($id);

            $group->updateFromRequest(UpdateGroupRequest::createFromArray($this->request->getParsedBody()));
        } else {
            $group = Group::createFromRequest(
                CreateGroupRequest::createFromArray($this->request->getParsedBody())
            );
        }

        $repository->save($group);

        return new \Slim\Psr7\Response(StatusCodeInterface::STATUS_OK);
    }
}