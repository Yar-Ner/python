<?php
declare(strict_types=1);

namespace App\Domain\Vehicle\Types;


use App\Domain\Vehicle\Types\Request\CreateVehiclesTypesRequest;
use App\Domain\Vehicle\Types\Request\UpdateVehiclesTypesRequest;

class VehiclesTypes implements \JsonSerializable
{
    private $id;

    private $extId;

    private $name;

    private $description;

    private $containersId;

    private $deleted;

    public function __construct(
        ?int $id,
        ?string $extId = null,
        string $name,
        ?string $description,
        array $containersId = [],
        ?int $deleted
    )
    {
        $this->id = $id;
        $this->extId = $extId;
        $this->name = $name;
        $this->description = $description;
        $this->containersId = $containersId;
        $this->deleted = $deleted;
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getContainersId(): array
    {
      return $this->containersId;
    }

    public function getExtId(): ?string
    {
      return $this->extId;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'ext_id' => $this->extId,
            'name' => $this->name,
            'description' => $this->description,
            'containersId' => $this->containersId,
            'deleted' => $this->deleted
        ];
    }

    public static function createFromRequest(CreateVehiclesTypesRequest $request): VehiclesTypes
    {
        return new VehiclesTypes(null, $request->extId, $request->name, $request->description, $request->containersId, (int)$request->deleted);
    }

    public function updateFromRequest(UpdateVehiclesTypesRequest $request): void
    {
        $this->name = $request->name;
        if (isset($request->description)) $this->description = $request->description;
        if (isset($request->containersId)) $this->containersId = $request->containersId;
        if (isset($request->extId)) $this->extId = $request->extId;
        if (isset($request->deleted)) $this->deleted = $request->deleted;
    }
}