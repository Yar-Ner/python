<?php
declare(strict_types=1);

namespace App\Domain\Vehicle\Containers\Request;

class CreateVehiclesContainersRequest
{
    public $extId;

    public $name;

    public $description;

    public $vehicleId;

    public $units;

    public $volume;

    public $droppedOut;

    public static function createFromArray(array $data): CreateVehiclesContainersRequest
    {
        $obj = new self();

        $obj->extId = $data['ext_id'];
        $obj->name = $data['name'];
        $obj->description = $data['description'] ?? null;
        $obj->units = $data['units'] ?? null;
        $obj->volume = isset($data['volume']) ? (float)$data['volume'] : null;
        $obj->droppedOut = isset($data['dropped_out']) ? (int)$data['dropped_out'] : null;

        return $obj;
    }
}
