<?php


namespace App\Application\Actions\Task;


use App\Domain\SharedContext\Log\LogRequest;
use App\Domain\SharedContext\Log\LogRequestRepository;
use App\Domain\Task\Request\StatusRequest;
use Psr\Http\Message\ResponseInterface as Response;

class StatusTaskAction extends TaskAction
{

    protected function action(): Response
    {

      /** @var LogRequestRepository $logRequestRepository */
      $logRequestRepository = $this->get(LogRequestRepository::class);

      $taskId = (int) $this->resolveArg('id');
      $status = $this->resolveArg('status');
      $taskExtId = $this->taskRepository->getExtIdById($taskId);

      if (!in_array($status, ['draft', 'queued', 'process', 'done', 'failed']))
        return $this->respondWithData(['result' => false, 'message' => 'Переданный статус не является валидным.']);

      try {
        /** @var StatusRequest $request */
        $request = $this->getDenormalizedRequest(StatusRequest::class);

        $this->taskRepository->status($request, $status);

        if (in_array($status, ['done', 'failed']) && $request->locationId) {
          $task = $this->taskRepository->findAll(0, 0, $taskId)[0];
          $vehicleId = $task->getVehicleId();

          $this->monitoringRepository->setOrderIdToLocationPoints($taskId, $request->orderId, $request->locationId);

          $trackPoints = $this->monitoringRepository->getTrackByVehicle($vehicleId, ['tasks_id' => $taskId, 'orders_id' => $request->orderId]);
          $distance = $task->getDistanceByTrackPoints($trackPoints);
          $this->taskRepository->setDistance($request->orderId, $distance);
        }

        $orderExtId = $this->taskRepository->getOrderExtIdByOrderId($request->orderId);
        $res = $this->dataSender->sendTaskStatus(['task_id' => $taskExtId, 'id' => $orderExtId, 'status' => $status], 'order');
      } catch (\Error | \Exception $e) {
        $logRequest = LogRequest::createFromArray(
          date("Y-m-d H:i:s"),
          $this->user->getId(),
          $_SERVER['REQUEST_URI'],
          json_encode($request),
          $e->getMessage()
        );
        $logRequestId = $logRequestRepository->log($logRequest);
      }

      return $this->respondWithData(['id' => $taskId, 'webhook_res' => $res ?? null, "logRequestId" => $logRequestId ?? null]);
    }
}