<?php

declare(strict_types=1);


namespace App\Domain\Vehicle\Response;


use App\Domain\Vehicle\Vehicle;

class UserVehiclesResponse implements \JsonSerializable
{
    /**
     * @var Vehicle[]
     */
    private $vehicles;

    public function __construct(array $vehicles)
    {
        $this->vehicles = $vehicles;
    }

    public function jsonSerialize(): array
    {
        return array_map(static function(Vehicle $vehicle) {
            $return = [
                'id' => $vehicle->getId(),
                'name' => $vehicle->getName(),
                'number' => $vehicle->getNumber(),
                'description' => $vehicle->getDescription(),
                'weight' => $vehicle->getWeight(),
                'active' => $vehicle->getActive(),
            ];

            if ($vehicle->getType()) {
              $return['type'] = $vehicle->getType();
            }

            if ($vehicle->getContainers()) {
              $return['containers'] = $vehicle->getContainers();
            }

            return $return;
        }, $this->vehicles);
    }
}