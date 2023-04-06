<?php
declare(strict_types=1);

namespace App\Domain\Vehicle\Containers;


use App\Domain\Vehicle\Containers\Request\CreateVehiclesContainersRequest;
use App\Domain\Vehicle\Containers\Request\UpdateVehiclesContainersRequest;

class VehiclesContainers implements \JsonSerializable
{
    private $id;
    
    private $extId;
    
    private $name;

    private $description;

    private $units;

    private $volume;

    private $droppedOut;

    public function __construct(
        ?int $id,
        ?string $extId = null,
        string $name,
        ?string $description,
        ?string $units,
        ?float $volume,
        ?int $dropped_out
    )
    {
        $this->id = $id;
        $this->extId = $extId;
        $this->name = $name;
        $this->description = $description;
        $this->units = $units;
        $this->volume = $volume;
        $this->droppedOut = $dropped_out;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getExtId(): ?string
    {
      return $this->extId;
    }

    public function setId(int $id)
    {
      $this->id = $id;
    }

    public function getName(): string
    {
      return $this->name;
    }

    public function getDescription(): ?string
    {
      return $this->description;
    }

    public function getUnits(): ?string
    {
      return $this->units;
    }

    public function getVolume(): ?float
    {
      return $this->volume;
    }

    public function getDroppedOut(): ?int
    {
      return $this->droppedOut;
    }

    public function jsonSerialize(): array
    {
        $return = [
            'id' => $this->id,
            'ext_id' => $this->extId,
            'name' => $this->name,
            'description' => $this->description
        ];

        if ($this->units) $return['units'] = $this->units;
        if ($this->volume) $return['volume'] = $this->volume;
        if ($this->droppedOut) $return['dropped_out'] = $this->droppedOut;

        return $return;
    }

    public static function createFromRequest(CreateVehiclesContainersRequest $request): VehiclesContainers
    {
        return new VehiclesContainers(null, $request->extId, $request->name, $request->description, $request->units, $request->volume, $request->droppedOut);
    }

    public function updateFromRequest(UpdateVehiclesContainersRequest $request): void
    {
        $this->name = $request->name;
        if (isset($request->description)) $this->description = $request->description;
        if (isset($request->units)) $this->units = $request->units;
        if (isset($request->volume)) $this->volume = $request->volume;
        if (isset($request->droppedOut)) $this->droppedOut = $request->droppedOut;
        if (isset($request->extId)) $this->extId = $request->extId;
    }
}