<?php
declare(strict_types=1);

namespace App\Domain\Vehicle\Containers\Request;

class UpdateVehiclesContainersRequest
{
    public $id;

    public $extId;

    public $name;

    public $description;

    public $vehicleId;

    public $units;

    public $volume;

    public $droppedOut;

    public static function createFromArray(array $data): UpdateVehiclesContainersRequest
    {
        $obj = new self();

        $obj->name = $data['name'];
        if (isset($data['description'])) $obj->description = $data['description'];
        if (isset($data['units'])) $obj->units = $data['units'];
        if (isset($data['volume'])) $obj->volume = (float)$data['volume'];
        if (isset($data['dropped_out'])) $obj->droppedOut = (int)$data['dropped_out'];
        if (isset($data['ext_id'])) $obj->extId = $data['ext_id'];

        return $obj;
    }
}
