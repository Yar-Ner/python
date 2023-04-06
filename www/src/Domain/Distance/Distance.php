<?php
declare(strict_types=1);

namespace App\Domain\Distance;

use JsonSerializable;

class Distance implements JsonSerializable
{
    private $id;

    private $distance;

    public function __construct(string $id, int $distance)
    {
        $this->id = $id;
        $this->distance = $distance;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getDistance(): int
    {
        return $this->distance;
    }

    public function jsonSerialize(): array
    {
      return [
        'ext_id' => $this->id,
        'distance' => $this->distance
      ];
    }
}
