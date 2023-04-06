<?php
declare(strict_types=1);

namespace App\Domain\Vehicle\Request;

class CreateVehicleRequest
{
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

    public static function createFromArray(array $data): CreateVehicleRequest
    {
        $obj = new self();

        $obj->name = $data['name'];
        $obj->ext_id = $data['ext_id'];
        $obj->number = $data['number'];
        $obj->type = (int)$data['type'] ?? null;
        $obj->description = $data['description'] ?? null;
        $obj->usersId = isset($data['usersId']) && $data['usersId'] !== '' ? explode(',', $data['usersId']) : [];
        $obj->devicesId = isset($data['devicesId']) && $data['devicesId'] !== '' ? explode(',', $data['devicesId']) : [];
        $obj->color = $data['color'] ?? null;
        $obj->weight = $data['weight'] ?? null;
        $obj->active = $data['active'] ?? null;
        $obj->odometer = $data['odometer'] ?? null;

        return $obj;
    }
}
