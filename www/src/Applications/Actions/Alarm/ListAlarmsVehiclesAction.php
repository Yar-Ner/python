<?php

declare(strict_types=1);

namespace App\Application\Actions\Alarm;

use App\Domain\Alarm\Response\VehicleAlarmsResponse;
use Psr\Http\Message\ResponseInterface as Response;

class ListAlarmsVehiclesAction extends AlarmAction
{

    protected function action(): Response
    {
        $id = $this->request->getQueryParams()['id'] ?? 0;
        return $this->respondWithData(new VehicleAlarmsResponse($this->alarmRepository->findAllAlarmsVehicles((int)$id)));
    }
}
