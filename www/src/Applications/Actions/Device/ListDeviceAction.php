<?php
declare(strict_types=1);

namespace App\Application\Actions\Device;


use Psr\Http\Message\ResponseInterface as Response;

class ListDeviceAction extends DeviceAction
{

    protected function action(): Response
    {
        return $this->respondWithData($this->deviceRepository->findAll());
    }
}