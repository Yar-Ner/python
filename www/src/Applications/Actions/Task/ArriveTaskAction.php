<?php


namespace App\Application\Actions\Task;


use App\Application\Actions\Action;
use App\Domain\SharedContext\Log\LogRequest;
use App\Domain\SharedContext\Log\LogRequestRepository;
use App\Domain\Task\Request\ArrivalRequest;
use App\Domain\Task\TaskRepository;
use Error;
use Psr\Http\Message\ResponseInterface as Response;

class ArriveTaskAction extends Action
{
    protected function action(): Response
    {
        /** @var LogRequestRepository $logRequestRepository */
        $logRequestRepository = $this->get(LogRequestRepository::class);

        $taskId = (int) $this->resolveArg('id');
        try {
            $this->get(TaskRepository::class)->arrive(
                ArrivalRequest::createFromArray(
                    $this->request->getParsedBody()
                ), $taskId
            );

            return $this->respondWithData(['id' => $taskId]);
        } catch (Error|\Exception $e) {
            $logRequest = LogRequest::createFromArray(
                date("Y-m-d H:i:s"),
                $this->user->getId(),
                $_SERVER['REQUEST_URI'],
                json_encode(['task_id' => $taskId, 'body' => $this->request->getParsedBody()]),
                $e->getMessage()
            );
            $logRequestId = $logRequestRepository->log($logRequest);
            return $this->respondWithData([ 'res' => false, "message" => $e, "logRequestId" => $logRequestId], 400);
        }
    }
}