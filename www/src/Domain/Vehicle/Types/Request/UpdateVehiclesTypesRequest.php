<?php
declare(strict_types=1);

namespace App\Domain\Vehicle\Types\Request;

class UpdateVehiclesTypesRequest
{
    public $id;

    public $extId;

    public $name;

    public $description;

    public $containersId;

    public $deleted;

    public static function createFromArray(array $data): UpdateVehiclesTypesRequest
    {
        $obj = new self();

        $obj->name = $data['name'];
        if (isset($data['description'])) $obj->description = $data['description'];
        if (isset($data['containersId'])) $obj->containersId = $data['containersId'] === '' ? [] : explode(',', $data['containersId']);
        if (isset($data['ext_id'])) $obj->extId = $data['ext_id'];
        if (isset($data['deleted'])) $obj->deleted = $data['deleted'];

        return $obj;
    }
}
