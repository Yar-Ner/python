<?php

declare(strict_types=1);

namespace App\Domain\Monitoring\Response;


use App\Domain\Device\Device;
use App\Domain\Task\Task;
use App\Domain\Vehicle\Vehicle;

class MonitoringResponse implements \JsonSerializable
{
    private $track;

    private $task;

    private $vehicle;

    public function __construct(array $track, Task $task, Vehicle $vehicle)
    {
        $this->track = $track;
        $this->task = $task;
        $this->vehicle = $vehicle;
    }

    public function jsonSerialize(): array
    {
        return [
          'starttime' => $this->task->getStarttime(),
          'endtime'  => $this->task->getEndtime(),
          'number' => $this->task->getNumber(),
          'driverName' =>  $this->task->getDriverName(),
          'vehicleName' => $this->vehicle->getName(),
          'vehicleColor' => $this->vehicle->getColor(),
          'distance'  => $this->task->getDistance(),
          'track'     => $this->track
        ];
    }
}