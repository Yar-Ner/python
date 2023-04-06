<?php
declare(strict_types=1);

namespace App\Domain\Geo;

use App\Domain\Geo\Request\CreateGeoRequest;
use App\Domain\Geo\Request\UpdateGeoRequest;
use JsonSerializable;

class Geo implements JsonSerializable {
    /**
     * @var int|null
     */
    private $id;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $type;

    /**
     * @var string
     */
    public $address;

    /**
     * @var float
     */
    public $lat;

    /**
     * @var float
     */
    public $long;

    /**
     * @var float
     */
    public $radius;

    /**
     * @var int|null
     */
    public $deleted;

    /**
     * @var string|null
     */
    public $ext_id;

    public function __construct(
        ?int $id,
        string $name,
        string $type,
        string $address,
        float $lat,
        float $long,
        float $radius,
        ?int $deleted,
        ?string $ext_id
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->type = $type;
        $this->address = $address;
        $this->lat = $lat;
        $this->long = $long;
        $this->radius = $radius;
        $this->deleted = $deleted;
        $this->ext_id = $ext_id;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'type' => $this->type,
            'address' => $this->address,
            'lat' => $this->lat,
            'long' => $this->long,
            'radius' => $this->radius,
            'deleted' => $this->deleted,
            'ext_id' => $this->ext_id
        ];
    }

    public function getLat(): float
    {
        return (float)$this->lat;
    }

    public function getLong(): float
    {
        return (float)$this->long;
    }

    public function getRadius(): float
    {
        return (float)$this->radius;
    }

    public function getDeleted(): int
    {
        return (int)$this->deleted;
    }

    public function getExtId(): ?string
    {
      return $this->ext_id;
    }

    public static function createFromRequest(CreateGeoRequest $request): Geo
    {
        return new Geo(
            null,
            $request->name,
            $request->type,
            $request->address,
            $request->lat,
            $request->long,
            $request->radius,
            $request->deleted,
            $request->ext_id
        );
    }

    public function UpdateFromRequest(UpdateGeoRequest $request): void
    {
        if ($request->name) $this->name = $request->name;
        if ($request->type) $this->type = $request->type;
        if ($request->address) $this->address = $request->address;
        if ($request->lat) $this->lat = $request->lat;
        if ($request->long) $this->long = $request->long;
        if ($request->radius) $this->radius = $request->radius;
        if ($request->deleted) $this->deleted = $request->deleted;
        if ($request->ext_id) $this->ext_id = $request->ext_id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }
}