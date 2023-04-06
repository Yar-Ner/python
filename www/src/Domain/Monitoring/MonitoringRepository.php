<?php
declare(strict_types=1);

namespace App\Domain\Monitoring;

use App\Domain\Monitoring\Request\ViewMonitoringRequest;

interface MonitoringRepository
{
    /**
     * @return Monitoring[]
     */
    public function findVehicleLocation(?int $vehiclesId): array;

    public function save(Monitoring $monitoring): int;

    public function getTrackByVehicle(int $vehicleId, $request): ?array;

    public function setOrderIdToLocationPoints(int $taskId, int $orderId, int $locationId);
}
