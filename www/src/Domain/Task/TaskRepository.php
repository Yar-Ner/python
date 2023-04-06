<?php
declare(strict_types=1);

namespace App\Domain\Task;

use App\Domain\Distance\TaskDistance;
use App\Domain\Task\Request\ArrivalRequest;
use App\Domain\Task\Request\AssignRequest;
use App\Domain\Task\Request\DepartureRequest;
use App\Domain\Task\Request\Modify\ModifyRequest;
use App\Domain\Task\Request\PayloadRequest;
use App\Domain\Task\Request\StatusRequest;
use App\Domain\Task\Request\WeightRequest;

interface TaskRepository
{
    /**
     * @return Task[]
     */
    public function findAll(int $pos, int $count, int $taskId, array $filters, ?int $onlyCount = 0, $userRules = [10]);

    public function start(int $taskId, string $time, int $userId): void;

    public function finish(int $taskId, string $time): void;

    public function pause(int $taskId, string $time): void;

    public function arrive(ArrivalRequest $request, int $taskId): void;

    public function departure(DepartureRequest $request, int $taskId): void;

    public function modify(ModifyRequest $request): int;

    public function delete(string $ext_id): int;

    public function assign(AssignRequest $request, int $taskId): void;

    public function status(StatusRequest $request, string $status): void;

    public function findAddressesByTaskId(int $taskId): ?array;

    public function setDistance(int $orderId, int $distance, ?string $type = ""): void;

    public function getDistance(int $tasksId): TaskDistance;

    public function getExtIdById(int $id): string;

    public function getOrderExtIdByOrderId(int $id): string;

    public function setWeight(string $taskExtId, WeightRequest $request): void;

    public function setTaskOdometer($taskId, $odometer): void;

    public function setPayload(PayloadRequest $request): void;
}
