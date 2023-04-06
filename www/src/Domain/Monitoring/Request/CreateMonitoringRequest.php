<?php
declare(strict_types=1);

namespace App\Domain\Monitoring\Request;

class CreateMonitoringRequest
{
    public $vehicle_id;

    public $time;

    public $latitude;

    public $longitude;

    public $speed;

    public $distance;

    public $direction;

    public $tasks_id;

    public $orders_id;

    public $altitude;

    public $accuracy;

  public static function createFromArray(array $data): CreateMonitoringRequest
    {
        $obj = new self();

        $obj->vehicle_id = (int) $data['vehicle_id'];
        $obj->time = $data['time'];
        $obj->latitude = $data['lat'];
        $obj->longitude = $data['long'];
        $obj->speed = (int) $data['speed'];
        $obj->distance = (int) $data['distance'];
        if (isset($data['tasks_id'])) $obj->tasks_id = (int) $data['tasks_id'];
        if (isset($data['orders_id'])) $obj->orders_id = (int) $data['orders_id'];
        $obj->direction = (int) $data['direction'];
        $obj->altitude = (int) $data['altitude'];
        $obj->accuracy = (int) $data['accuracy'];

        return $obj;
    }
}
