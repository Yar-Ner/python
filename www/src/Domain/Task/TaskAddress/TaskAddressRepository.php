<?php
declare(strict_types=1);

namespace App\Domain\Task\TaskAddress;

use App\Domain\Task\TaskOrder\TaskOrderRepository;

interface TaskAddressRepository
{
    public function modifyArray(int $taskId, ?array $addressRequests, TaskOrderRepository $taskOrderRepository, ?int$geoRadius = 15): void;
}
