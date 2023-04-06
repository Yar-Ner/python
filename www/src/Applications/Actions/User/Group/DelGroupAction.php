<?php
declare(strict_types=1);

namespace App\Application\Actions\User\Group;

use App\Application\Actions\Action;
use App\Domain\User\Group\GroupRepository;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface as Response;

class DelGroupAction extends Action
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $id = (int) $this->request->getAttribute('id');

        /** @var GroupRepository $repository */
        $repository = $this->get(GroupRepository::class);

        return $this->respondWithData($repository->delete($id));
    }
}