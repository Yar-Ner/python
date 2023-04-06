<?php
declare(strict_types=1);

namespace App\Application\Actions\Device;

use Psr\Http\Message\ResponseInterface as Response;

class ViewDeviceAction extends DeviceAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $deviceId = (int) $this->resolveArg('id');

        return $this->respondWithData($this->deviceRepository->getById($deviceId));
    }
}