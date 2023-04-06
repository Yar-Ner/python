<?php
declare(strict_types=1);

namespace App\Domain\Device\Request;

class CreateDeviceRequest
{
    public $name;

    public $imei;

    public $vehicleId;

    public static function createFromArray(array $data): CreateDeviceRequest
    {
        $obj = new self();

        $obj->name = $data['name'];
        $obj->imei = $data['imei'];
        $obj->vehicleId = isset($data['vehicleId']) && $data['vehicleId'] !== '' ? explode(',', $data['vehicleId']) : [];

        return $obj;
    }
}
