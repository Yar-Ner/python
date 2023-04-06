<?php

declare(strict_types=1);

namespace App\Application\Actions\Alarm;

use Psr\Http\Message\ResponseInterface as Response;

class ChangeActiveVehicleAlarmAction extends AlarmAction
{

  protected function action(): Response
  {
    $id = (int) $this->resolveArg('id');
    return $this->respondWithData(['id' => $this->alarmRepository->changeActiveVehicleAlarmAction($id)]);
  }
}
