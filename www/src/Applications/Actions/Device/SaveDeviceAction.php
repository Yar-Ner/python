<?php
declare(strict_types=1);

namespace App\Application\Actions\Device;


use App\Domain\Device\Device;
use App\Domain\Device\Request\CreateDeviceRequest;
use App\Domain\Device\Request\UpdateDeviceRequest;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface as Response;

class SaveDeviceAction extends DeviceAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $id = (int) $this->request->getAttribute('id');

        if ($id) {
            $device = $this->deviceRepository->getById($id);

            $device->updateFromRequest(UpdateDeviceRequest::createFromArray($this->request->getParsedBody()));
        } else {
            $device = Device::createFromRequest(
                CreateDeviceRequest::createFromArray($this->request->getParsedBody())
            );
        }

        $this->deviceRepository->save($device);

        return new \Slim\Psr7\Response(StatusCodeInterface::STATUS_OK);
    }
}