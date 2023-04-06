<?php


namespace App\Application\Actions\Task;

use App\Domain\Vehicle\VehicleRepository;
use Psr\Http\Message\ResponseInterface as Response;

class FinishTaskAction extends TaskAction
{

  /**
   * @throws \Exception
   */
  protected function action(): Response
    {
        $taskId = (int) $this->resolveArg('id');
        $time = $this->request->getParsedBody()['time'] ?? date("Y-m-d H:i:s");
        $odometer = $this->request->getParsedBody()['odometer'] ?? null;

        $this->taskRepository->finish($taskId, $time);
        /** @var VehicleRepository $vehicleRepository*/

        $task = $this->taskRepository->findAll(0, 0, $taskId);
        $taskExtId = $this->taskRepository->getExtIdById($taskId);
        if (count($task) > 0) {
          $task = $task[0];
          $vehicleId = $task->getVehicleId();

          if ($odometer) {
            $vehicleRepository = $this->get(VehicleRepository::class);
            $vehicle = $vehicleRepository->getById($vehicleId, $this->user->getRulesId());
            $vehicle->setOdometer($odometer);
            $vehicleRepository->save($vehicle);
            $this->taskRepository->setTaskOdometer($taskId, $odometer);
            $odometerRes = $this->dataSender->sendOdometerValue([
              'task_id' => $taskExtId,
              'odometer' => $odometer
            ]);
          }

          $trackPoints = $this->monitoringRepository->getTrackByVehicle($vehicleId, ['tasks_id' => $taskId]);
          $distance = $task->getDistanceByTrackPoints($trackPoints);
          $this->taskRepository->setDistance($taskId, $distance, 'task');
        }

        $arrayDistance = $this->taskRepository->getDistance($taskId);
        $distanceRes = $this->dataSender->sendTaskDistance($arrayDistance);
        $statusRes = $this->dataSender->sendTaskStatus(['id' => $taskExtId, 'status' => 'finish'], 'task');

        return $this->respondWithData([
          'id' => $taskId,
          'webhook_distance_res' => $distanceRes,
          'webhook_status_res' => $statusRes,
          'webhook_odometer_res' => $odometerRes ?? null
        ]);
    }
}