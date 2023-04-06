<?php


namespace App\Application\Actions\Task;

use Psr\Http\Message\ResponseInterface as Response;

class PauseTaskAction extends TaskAction
{

    protected function action(): Response
    {
        $taskId = (int) $this->resolveArg('id');
        $time = $this->request->getParsedBody()['time'] ?? date("Y-m-d H:i:s");

        $this->taskRepository->pause($taskId, $time);

        $taskExtId = $this->taskRepository->getExtIdById($taskId);
        $res = $this->dataSender->sendTaskStatus(['id' => $taskExtId, 'status' => 'pause'], 'task');

        return $this->respondWithData(['id' => $taskId, 'webhook_res' => $res ?? null]);
    }
}