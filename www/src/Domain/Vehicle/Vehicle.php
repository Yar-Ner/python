<?php
declare(strict_types=1);

namespace App\Domain\Vehicle;


use App\Domain\Vehicle\Request\CreateVehicleRequest;
use App\Domain\Vehicle\Request\UpdateVehicleRequest;

class Vehicle implements \JsonSerializable
{
    private $id;

    private $name;

    private $number;

    private $type;

    private $containers;

    private $description;

    private $usersId;

    private $devicesId;

    private $ext_id;

    private $color;

    private $weight;

    private $active;

    private $odometer;

  public function __construct(
      ?int $id,
      string $name,
      string $number,
      ?int $type,
      ?array $containers,
      ?string $description,
      array $usersId = [],
      array $devicesId = [],
      ?string $ext_id,
      ?string $color,
      ?int $weight,
      ?int $active,
      ?int $odometer
  )
    {
        $this->id = $id;
        $this->name = $name;
        $this->number = $number;
        $this->type = $type;
        $this->containers = $containers;
        $this->description = $description;
        $this->usersId = $usersId;
        $this->devicesId = $devicesId;
        $this->ext_id = $ext_id;
        $this->color = $color;
        $this->weight = $weight;
        $this->active = $active;
        $this->odometer = $odometer;
    }

    public function jsonSerialize(): array
    {
        $return = [
            'id' => $this->id,
            'name' => $this->name,
            'number' => $this->number,
            'type' => $this->type,
            'description' => $this->description,
            'usersId' => $this->usersId,
            'devicesId' => $this->devicesId,
            'ext_id' => $this->ext_id,
            'color' => $this->color,
            'weight' =>$this->weight,
            'value' => $this->name,
            'active' => $this->active,
            'odometer' => $this->odometer
        ];

        if ($this->containers) $return['containers'] = $this->containers;

        return $return;
    }

    public function getUsersId(): array
    {
        return $this->usersId;
    }

    public function getDevicesId(): array
    {
        return $this->devicesId;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getNumber(): string
    {
        return $this->number;
    }

    public function getType(): ?int
    {
      return $this->type;
    }

    public function getContainers(): ?array
    {
      return $this->containers;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getExtId(): ?string
    {
      return $this->ext_id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getColor(): ?string
    {
      return $this->color;
    }

    public function getWeight(): ?int
    {
      return (int)$this->weight;
    }

    public function getActive(): ?int
    {
      return (int)$this->active;
    }

    public function getOdometer(): ?int
    {
      return (int)$this->odometer;
    }

    public function setOdometer(int $odometer)
    {
      $this->odometer = $odometer;
    }

    public static function createFromRequest(CreateVehicleRequest $request): Vehicle
    {
        return new Vehicle(
          null,
          $request->name,
          $request->number,
          $request->type,
          null,
          $request->description,
          $request->usersId,
          $request->devicesId,
          $request->ext_id,
          $request->color,
          (int)$request->weight,
          (int)$request->active,
          (int)$request->odometer
        );
    }

    public function updateFromRequest(UpdateVehicleRequest $updateVehicleRequest): void
    {
        $this->name = $updateVehicleRequest->name;
        $this->number = $updateVehicleRequest->number;
        if (isset($updateVehicleRequest->type)) $this->type = $updateVehicleRequest->type;
        if (isset($updateVehicleRequest->description)) $this->description = $updateVehicleRequest->description;
        if (isset($updateVehicleRequest->usersId)) $this->usersId = $updateVehicleRequest->usersId;
        if (isset($updateVehicleRequest->devicesId)) $this->devicesId = $updateVehicleRequest->devicesId;
        if (isset($updateVehicleRequest->ext_id)) $this->ext_id = $updateVehicleRequest->ext_id;
        if (isset($updateVehicleRequest->color)) $this->color = $updateVehicleRequest->color;
        if (isset($updateVehicleRequest->weight)) $this->weight = $updateVehicleRequest->weight;
        if (isset($updateVehicleRequest->active)) $this->active = $updateVehicleRequest->active;
        if (isset($updateVehicleRequest->odometer)) $this->odometer = $updateVehicleRequest->odometer;
    }
}