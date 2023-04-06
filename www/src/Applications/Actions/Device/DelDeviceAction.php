<?php
declare(strict_types=1);

namespace App\Application\Actions\Device;

use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface as Response;

class DelDeviceAction extends DeviceAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $id = (int) $this->request->getAttribute('id');

        $this->deviceRepository->delete($id);

        return new \Slim\Psr7\Response(StatusCodeInterface::STATUS_OK);
    }
}