<?php
declare(strict_types=1);

namespace App\Domain\Device;

use App\Domain\Device\Request\CreateDeviceRequest;
use App\Domain\Device\Request\UpdateDeviceRequest;
use JsonSerializable;

class Device implements JsonSerializable
{
    private $id;

    private $name;

    private $imei;

    private $vehicleId;

    public function __construct(?int $id, string $name, string $imei, array $vehicleId = [])
    {
        $this->id = $id;
        $this->name = $name;
        $this->imei = $imei;
        $this->vehicleId = $vehicleId;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'imei' => $this->imei,
            'vehicleId' => $this->vehicleId
        ];
    }

    public function getImei(): string
    {
        return $this->imei;
    }

    public static function createFromRequest(CreateDeviceRequest $request): Device
    {
        return new Device(null, $request->name, $request->imei, $request->vehicleId);
    }

    public function updateFromRequest(UpdateDeviceRequest $updateDeviceRequest): void
    {
        $this->name = $updateDeviceRequest->name;
        $this->imei = $updateDeviceRequest->imei;
        $this->vehicleId = $updateDeviceRequest->vehicleId ?? $this->vehicleId;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getVehicleId(): array
    {
        return $this->vehicleId;
    }
}
