<?php
declare(strict_types=1);

namespace App\Domain\Task\TaskOrder;

interface TaskOrderRepository
{
    /**
     * @param int $id
     * @return array
     */
    public function getById(int $id): array;

    public function modifyArray(array $orderRequests, int $taId): void;
}