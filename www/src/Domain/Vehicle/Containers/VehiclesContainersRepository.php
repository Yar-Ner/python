<?php
declare(strict_types=1);

namespace App\Domain\Vehicle\Containers;

interface VehiclesContainersRepository
{
    /**
     * @return VehiclesContainers[]
     */
    public function findAll(): array;

    public function getById(int $id): VehiclesContainers;

    public function getByExtId(string $extId): ?VehiclesContainers;

    public function save(VehiclesContainers $vehicleContainer): int;

    public function delete(int $id): int;

}
