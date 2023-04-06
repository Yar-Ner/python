<?php
declare(strict_types=1);

namespace App\Domain\Vehicle;

use Slim\Exception\HttpNotFoundException;

interface VehicleRepository
{
    /**
     * @return Vehicle[]
     */
    public function findAll(array $userRules): array;

    /**
     * @throws HttpNotFoundException
     */
    public function getById(int $id, array $userRules = []);

    public function check1cVehicle(string $ext_id): ?Vehicle;

    public function save(Vehicle $vehicle): int;

    public function delete(int $id): void;

    /**
     * @param int $id
     * @return Vehicle[]
     */
    public function findAllByUserId(int $id, array $userRules): array;

}
