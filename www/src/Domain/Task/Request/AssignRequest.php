<?php


namespace App\Domain\Task\Request;


class AssignRequest
{
    public $vehiclesId;

    public $time;

    public static function createFromArray(array $data): self
    {
      $request = new self();

      $request->vehiclesId = $data['vehicles_id'];
      $request->time  = $data['time'] ?? date("Y-m-d H:i:s");

      return $request;
    }
}
