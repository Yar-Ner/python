<?php
declare(strict_types=1);

namespace App\Domain\Monitoring;

use App\Domain\Monitoring\Request\CreateMonitoringRequest;
use JsonSerializable;

class Monitoring implements JsonSerializable
{
    private $id;

    private $vehicles_id;

    private $time;

    private $latitude;

    private $longitude;

    private $speed;

    private $distance;

    private $direction;

    private $tasks_id;

    private $orders_id;

    private $altitude;

    private $accuracy;

    private $photos;

    private $color;

    private $name;

  public function __construct(
    ?int $id,
    int $vehicles_id,
    string $time,
    string $latitude,
    string $longitude,
    int $speed,
    int $distance,
    ?int $tasks_id,
    ?int $orders_id,
    int $direction,
    int $altitude,
    int $accuracy,
    ?array $photos
  )
    {
        $this->id = $id;
        $this->vehicles_id = $vehicles_id;
        $this->time = $time;
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        $this->speed = $speed;
        $this->distance = $distance;
        $this->direction = $direction;
        $this->tasks_id = $tasks_id;
        $this->orders_id = $orders_id;
        $this->altitude = $altitude;
        $this->accuracy = $accuracy;
        $this->photos = $photos;
    }

    public function jsonSerialize(): array
    {
        $return = [
            'id' => $this->id,
            'vehicles_id' => $this->vehicles_id,
            'time' => $this->time,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'speed' => $this->speed,
            'distance' => $this->distance,
            'direction' => $this->direction,
            'tasks_id' => $this->tasks_id,
            'orders_id' => $this->orders_id,
            'altitude' => $this->altitude,
            'accuracy' => $this->accuracy
        ];

        if ($this->photos) $return['photos'] = $this->photos;
        if ($this->color) $return['color'] = $this->color;
        if ($this->name) $return['name'] = $this->name;

        return $return;
    }

    public function getId(): ?int
    {
      return $this->id;
    }
    public function setId(int $id): void
    {
      $this->id = $id;
    }
    public function getVehiclesId(): int
    {
      return $this->vehicles_id;
    }
    public function getTime(): string
    {
      return $this->time;
    }
    public function getLatitude(): string
    {
      return $this->latitude;
    }
    public function getLongitude(): string
    {
      return $this->longitude;
    }
    public function getSpeed(): int
    {
      return $this->speed;
    }
    public function getDistance(): int
    {
      return $this->distance;
    }
    public function getDirection(): int
    {
      return $this->direction;
    }
    public function getTasksId(): ?int
    {
      return $this->tasks_id;
    }
    public function getOrdersId(): ?int
    {
      return $this->orders_id;
    }
    public function getAltitude(): int
    {
      return $this->altitude;
    }
    public function getAccuracy(): int
    {
      return $this->accuracy;
    }

    public function setColor(string $color)
    {
      $this->color = $color;
    }

    public function setName(string $name)
    {
      $this->name = $name;
    }

    public static function createFromRequest(CreateMonitoringRequest $request): Monitoring
    {
      return new Monitoring(
        null,
        $request->vehicle_id,
        $request->time,
        $request->latitude,
        $request->longitude,
        $request->speed,
        $request->distance,
        $request->tasks_id,
        $request->orders_id,
        $request->direction,
        $request->altitude,
        $request->accuracy,
        null
      );
    }
}
