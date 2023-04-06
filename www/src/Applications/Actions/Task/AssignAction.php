<?php


namespace App\Application\Actions\Task;


use App\Application\Actions\Action;
use App\Domain\Task\Request\AssignRequest;
use App\Domain\Task\TaskRepository;
use Psr\Http\Message\ResponseInterface as Response;

class AssignAction extends Action
{

    protected function action(): Response
    {
        $taskId = (int) $this->resolveArg('id');

        /** @var TaskRepository $taskRepository */
        $taskRepository = $this->get(TaskRepository::class);

        $taskRepository->assign(AssignRequest::createFromArray(
          $this->request->getParsedBody()
        ), $taskId);

        return $this->respondWithData(['id' => $taskId]);
    }
}