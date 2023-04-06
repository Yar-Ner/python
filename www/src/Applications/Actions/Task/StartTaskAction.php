<?php


namespace App\Application\Actions\Task;

use Psr\Http\Message\ResponseInterface as Response;

class StartTaskAction extends TaskAction
{

    protected function action(): Response
    {
        $taskId = (int) $this->resolveArg('id');
        $time = $this->request->getParsedBody()['time'] ?? date("Y-m-d H:i:s");

        $this->taskRepository->start($taskId, $time, $this->user->getId());

        $taskExtId = $this->taskRepository->getExtIdById($taskId);
        $res = $this->dataSender->sendTaskStatus(['id' => $taskExtId, 'status' => 'start'], 'task');

        return $this->respondWithData(['id' => $taskId, 'webhook_res' => $res ?? null]);
    }
}