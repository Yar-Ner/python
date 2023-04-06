<?php
declare(strict_types=1);

namespace App\Application\Actions\Monitoring;

use App\Domain\Monitoring\Request\ViewMonitoringRequest;
use App\Domain\Monitoring\Response\MonitoringResponse;
use Psr\Http\Message\ResponseInterface as Response;

class ViewMonitoringAction extends MonitoringAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $vehicleId = (int) $this->request->getAttribute('id');

        $request = $this->request->getQueryParams();
        $task = $this->taskRepository->findAll(0, 0, (int)$request['tasks_id'], [], 0, $this->user->getRulesId());
        $vehicle = $this->vehicleRepository->getById($vehicleId, $this->user->getRulesId());

        return $this->respondWithData(
          new MonitoringResponse(
            $this->monitoringRepository->getTrackByVehicle($vehicleId, $request),
            $task[0],
            $vehicle
          )
        );
    }
}