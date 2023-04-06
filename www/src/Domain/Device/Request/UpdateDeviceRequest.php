<?php
declare(strict_types=1);

namespace App\Domain\Device\Request;

class UpdateDeviceRequest
{
    public $id;

    public $name;

    public $imei;

    public $vehicleId;

    public static function createFromArray(array $data): UpdateDeviceRequest
    {
        $obj = new self();

        $obj->id = $data['id'];
        $obj->name = $data['name'];
        $obj->imei = $data['imei'];
        $obj->vehicleId = isset($data['vehicleId']) && $data['vehicleId'] !== '' ? explode(',', $data['vehicleId']) : [];

        return $obj;
    }
}
