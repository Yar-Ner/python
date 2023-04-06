<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Task\TaskOrder;

use App\Domain\Task\TaskOrder\TaskOrder;
use App\Domain\Task\TaskOrder\TaskOrderRepository;

class MySQLTaskOrderRepository implements TaskOrderRepository
{
    /**
     * @var \PDO[]
     */
    private $connection;

    public function __construct(\PDO $connection)
    {
        $this->connection = $connection;
    }

    public function getById(int $id): array
    {
        $sql = 'SELECT id, task_addresses_id, ext_id, action, volume, weight, gross_weight, package_weight, status, 
       failed_reason, plan_arrival, plan_departure, fact_arrival, fact_departure, payload, comment, geoobject_id
       FROM tasks_orders WHERE task_addresses_id=' . $id;
        $result = [];
        $res = $this->connection->query($sql);

        while ($row = $res->fetch()) {
            $result[] = new TaskOrder(
                $row['id'],
                $row['task_addresses_id'],
                $row['ext_id'],
                $row['action'],
                $row['volume'],
                $row['weight'],
                $row['gross_weight'],
                $row['package_weight'],
                $row['status'],
                $row['failed_reason'],
                $row['plan_arrival'],
                $row['plan_departure'],
                $row['fact_arrival'],
                $row['fact_departure'],
                $row['payload'],
                $row['comment'],
                $row['geoobject_id']
            );
        }

        return $result;
    }

    public function modifyArray(array $orderRequests, int $taId): void
    {
        array_walk($orderRequests, function($request) use ($taId) {
            $this->modify($request, $taId);
        });
    }

    private function modify($request, int $taId): void
    {
        $csql = "SELECT id, ext_id FROM tasks_orders WHERE ext_id = '".$request['ext_id']."'";
        $cres = $this->connection->query($csql);
        $crow = $cres->fetch();

        $sql = $crow ? "UPDATE" : "INSERT INTO";
        $sql .= sprintf(' tasks_orders SET ');
        foreach ($request as $key => $value) {
          if (in_array($key, ['id', 'task_addresses_id', 'action'])) continue;
          if ($key == 'payload' && $value != null) {
            $sql .= "`payload` = '".json_encode($value, JSON_UNESCAPED_UNICODE)."', ";
            continue;
          }
          if ($value) $sql .= "`$key` = '$value', ";
        }
        $sql .= "`action` = '".$request['action']."', ";
        $sql .= "`task_addresses_id` = '$taId', ";
        $sql = rtrim($sql, ', ');
        if ($crow) $sql .= " WHERE ext_id = '".$request['ext_id']."'";
        $this->connection->exec($sql);
    }
}