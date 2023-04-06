<?php
declare(strict_types=1);

namespace App\Domain\Vehicle\Types\Request;

class CreateVehiclesTypesRequest
{
    public $name;

    public $description;

    public $containersId;

    public $extId;

    public $deleted;

    public static function createFromArray(array $data): CreateVehiclesTypesRequest
    {
        $obj = new self();

        $obj->name = $data['name'];
        $obj->description = $data['description'] ?? null;
        $obj->extId = $data['ext_id'] ?? null;
        $obj->containersId = isset($data['containersId']) && $data['containersId'] !== '' ? explode(',', $data['containersId']) : [];
        $obj->deleted = $data['deleted'] ?? null;

        return $obj;
    }
}
