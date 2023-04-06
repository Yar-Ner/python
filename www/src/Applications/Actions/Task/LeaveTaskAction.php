<?php


namespace App\Application\Actions\Task;


use App\Application\Actions\Action;
use App\Domain\Task\Request\DepartureRequest;
use App\Domain\Task\TaskRepository;
use Psr\Http\Message\ResponseInterface as Response;

class LeaveTaskAction extends Action
{

    protected function action(): Response
    {
        $taskId = (int) $this->resolveArg('id');

        $this->get(TaskRepository::class)->departure(
            DepartureRequest::createFromArray(
                $this->request->getParsedBody()
            ), $taskId
        );

        return $this->respondWithData(['id' => $taskId]);
    }
}