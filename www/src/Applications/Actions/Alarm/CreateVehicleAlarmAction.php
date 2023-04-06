<?php

declare(strict_types=1);


namespace App\Application\Actions\Alarm;


use App\Domain\Alarm\Request\CreateVehicleAlarmRequest;
use Psr\Http\Message\ResponseInterface as Response;

class CreateVehicleAlarmAction extends AlarmAction
{
    protected function action(): Response
    {
        return $this->respondWithData([
            'id' => $this->alarmRepository->createVehicleAlarm(
                    CreateVehicleAlarmRequest::createFromArray(
                        $this->request->getParsedBody()
                    )
                )
      ]);
    }
}