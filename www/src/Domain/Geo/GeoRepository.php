<?php
declare(strict_types=1);

namespace App\Domain\Geo;

interface GeoRepository
{
    /**
     * @return Geo[]
     */
    public function findAll($geoIds, int $areas, int $pos, int $count, ?int $onlyCount, array $filters, int $short = 0);

    public function delete(int $id): void;

    /**
     * @throws GeoNotFoundException
     */
    public function getById(int $id): Geo;

    public function check1cGeoobject(string $ext_id): ?Geo;

    public function save(Geo $geo): int;

}
