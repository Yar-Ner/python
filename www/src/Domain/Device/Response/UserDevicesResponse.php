<?php

declare(strict_types=1);

namespace App\Domain\Device\Response;


use App\Domain\Device\Device;

class UserDevicesResponse implements \JsonSerializable
{
    private $devices;

    public function __construct(array $devices)
    {
        $this->devices = $devices;
    }

    public function jsonSerialize(): array
    {
        return array_map(static function(Device $device) {
            return [
                'id' => $device->getId(),
                'name' => $device->getName(),
                'imei' => $device->getImei()
            ];
        }, $this->devices);
    }
}