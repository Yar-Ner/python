<?php

declare(strict_types=1);

namespace App\Application\Actions\Alarm;

use App\Domain\Alarm\Response\AlarmsResponse;
use Psr\Http\Message\ResponseInterface as Response;

class ListAlarmsAction extends AlarmAction
{

    protected function action(): Response
    {
        return $this->respondWithData(new AlarmsResponse($this->alarmRepository->findAll()));
    }
}
