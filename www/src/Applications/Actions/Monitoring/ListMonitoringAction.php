<?php
declare(strict_types=1);

namespace App\Application\Actions\Monitoring;


use App\Domain\Monitoring\Request\ListMonitoringRequest;
use Psr\Http\Message\ResponseInterface as Response;

class ListMonitoringAction extends MonitoringAction
{

    protected function action(): Response
    {
      $vehiclesId = $this->request->getQueryParams()['vehicles_id'] ?? 0;

      return $this->respondWithData($this->monitoringRepository->findVehicleLocation((int)$vehiclesId));
    }
}