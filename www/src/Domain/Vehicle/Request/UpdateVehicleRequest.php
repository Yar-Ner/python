<?php
declare(strict_types=1);

namespace App\Domain\Vehicle\Request;

class UpdateVehicleRequest
{
    public $id;

    public $name;

    public $number;

    public $type;

    public $description;

    public $usersId;

    public $devicesId;

    public $ext_id;

    public $color;

    public $weight;

    public $active;

    public $odometer;

    public static function createFromArray(array $data): UpdateVehicleRequest
    {
        $obj = new self();

        $obj->ext_id = $data['ext_id'];
        $obj->name = $data['name'];
        $obj->number = $data['number'];
        if (isset($data['type'])) $obj->type = (int)$data['type'];
        if (isset($data['description'])) $obj->description = $data['description'];
        if (isset($data['usersId'])) $obj->usersId = $data['usersId'] === '' ? [] : explode(',', $data['usersId']);
        if (isset($data['devicesId'])) $obj->devicesId = $data['devicesId'] === '' ? [] : explode(',', $data['devicesId']);
        if (isset($data['color'])) $obj->color = $data['color'];
        if (isset($data['weight'])) $obj->weight = $data['weight'];
        if (isset($data['active'])) $obj->active = $data['active'];
        if (isset($data['odometer'])) $obj->odometer = $data['odometer'];

        return $obj;
    }
}
