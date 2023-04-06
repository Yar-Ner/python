<?php
declare(strict_types=1);

namespace App\Domain\Vehicle\Types;

interface VehiclesTypesRepository
{
    /**
     * @return VehiclesTypes[]
     */
    public function findAll(): array;

    public function getById(int $id): VehiclesTypes;

    public function getByExtId(string $extId): ?VehiclesTypes;

    public function save(VehiclesTypes $vehicleType): int;

    public function delete(int $id): int;

}
