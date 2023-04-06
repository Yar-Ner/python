<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Task;

use App\Domain\Distance\Distance;
use App\Domain\Distance\TaskDistance;
use App\Domain\Task\Request\ArrivalRequest;
use App\Domain\Task\Request\AssignRequest;
use App\Domain\Task\Request\DepartureRequest;
use App\Domain\Task\Request\Modify\ModifyRequest;
use App\Domain\Task\Request\PayloadRequest;
use App\Domain\Task\Request\StatusRequest;
use App\Domain\Task\Request\WeightRequest;
use App\Domain\Task\Task;
use App\Domain\Task\TaskAddress\TaskAddress;
use App\Domain\Task\TaskOrder\TaskOrder;
use App\Domain\Task\TaskRepository;

class MySQLTaskRepository implements TaskRepository
{
    /**
     * @var \PDO[]
     */
    private $connection;

    public function __construct(\PDO $connection)
    {
        $this->connection = $connection;
    }

    public function findAll(int $pos = 0, int $count = 0, int $taskId = 0, array $filters = [], ?int $onlyCount = 0, $userRules = [10])
    {
        $ljsql = '';
        $wsql = '';
        if (!in_array(8, $userRules)) $ljsql .= " \n LEFT JOIN vehicles ON (tasks.vehicles_id = vehicles.id)";
        if (!in_array(8, $userRules)) $wsql .= " AND vehicles.active = 1 ";

        $fsql ="";
        if (count($filters) > 0) {
          foreach ($filters as $key => $value) {
            if (!$value) continue;
            if ($key === 'starttime') {
              $datetime = json_decode($value);
              if ($datetime->start !== null && $datetime->end !== null) {
                $datetime->end = explode(' ', $datetime->end)[0] . " 23:59:59";
                $fsql .= "AND `$key` BETWEEN '$datetime->start' AND '$datetime->end' ";
              } else if ($datetime->start !== null) {
                $datetime->end = explode(' ', $datetime->start)[0] . " 23:59:59";
                $fsql .= "AND `$key` BETWEEN '$datetime->start' AND '$datetime->end' ";
              }
              continue;
            }
            $fsql .= "AND `$key` in ($value) ";
          }
        }

        if ($onlyCount) {
          $sql = "SELECT count(tasks.id) as count FROM tasks";
          $sql .= $ljsql;
          $sql .= " WHERE (tasks.deleted = 0 OR tasks.deleted IS NULL) ";
          $sql .= $wsql;

          if ($fsql !== '') {
            $sql .= $fsql;
          }

          $res = $this->connection->query($sql);
          $row = $res->fetch();

          return $row['count'];
        }
        $sql = 'SELECT tasks.id, user_id, vehicles_id, tasks.ext_id, tasks.number, status, comment, loaded_weight, 
                       empty_weight, starttime, endtime, updated, distance, tasks.deleted 
                FROM tasks';
        $sql .= $ljsql;
        $sql .= ' WHERE (tasks.deleted = 0 OR tasks.deleted IS NULL) ';
        $sql .= $wsql;

        if ($taskId) {
          $sql .= " AND tasks.id = $taskId ";
        }

        if ($fsql !== '') {
          $sql .= $fsql;
        }

        $sql .= "ORDER BY id DESC";

        if ($count) {
          $sql .= " LIMIT $pos, $count";
        }
        $result = [];
        $res = $this->connection->query($sql);

        while ($row = $res->fetch()) {
            $driverName=  null;
            if (isset($row['user_id'])) {
              $driverName = $this->getDriverNameByUserId($row['user_id']);
            }
            $result[] = new Task(
                $row['id'],
                $row['user_id'],
                $driverName,
                $row['vehicles_id'],
                $row['ext_id'],
                $row['number'],
                $row['status'],
                $row['loaded_weight'],
                $row['empty_weight'],
                $row['comment'],
                $row['starttime'],
                $row['endtime'],
                $row['updated'],
                $row['distance'],
                $this->findAddressesByTaskId($row['id'])
            );
        }

        return $result;
    }

    public function setDistance(int $orderId, int $distance, ?string $type = ""): void
    {
      $table = 'tasks_orders';
      if ($type === 'task') $table = 'tasks';
      $sql = "UPDATE $table SET `distance` = '$distance' WHERE `id` = '$orderId'";
      $this->connection->query($sql);
    }

    public function getOrdersDistanceByTaskId($tasksId): array
    {
      $sql = "SELECT ext_id, distance FROM tasks_geoobjects 
LEFT JOIN tasks_orders ON (tasks_geoobjects .id = tasks_orders.task_addresses_id)
WHERE tasks_id =" . $tasksId;
      $res = $this->connection->query($sql);

      $distanceOrdersArray = [];
      while ($row = $res->fetch()) {
        if ($row['distance'] !== null)
          $distanceOrdersArray[] = new Distance($row['ext_id'], $row['distance']);
      }

      return $distanceOrdersArray;
    }

    public function getDistance(int $tasksId): TaskDistance
    {
      $distanceArray = [];
      $sql = "SELECT id, ext_id, distance FROM tasks WHERE id = $tasksId";

      $res = $this->connection->query($sql);

      if ($row = $res->fetch()) {
        $ordersDistance = $this->getOrdersDistanceByTaskId($tasksId);
        $sumOrderDistance = 0;

        /** @var Distance $order */
        foreach ($ordersDistance as $order) {
          $sumOrderDistance += $order->getDistance();
        }
        $returnDistance = $row['distance'] - $sumOrderDistance;

        $distanceArray = new TaskDistance(
          $row['ext_id'],
          $row['distance'],
          $returnDistance,
          $ordersDistance
        );
      }

      return $distanceArray;
    }

    public function start(int $taskId, string $time, int $userId): void
    {
        $sql = "UPDATE tasks SET `status` = 'process', `user_id` = '$userId', starttime = '$time', `updated` = '$time' WHERE `id` = ".$taskId;

        $this->connection->exec($sql);
    }

    public function finish(int $taskId, string $time): void
    {
        $sql = "UPDATE tasks SET status = 'done', endtime = '$time', updated = '$time' WHERE id = ".$taskId;

        $this->connection->exec($sql);
    }

    public function pause(int $taskId, string $time): void
    {
        $sql = "UPDATE tasks SET status = 'queued', updated = '$time' WHERE id = ".$taskId;

        $this->connection->exec($sql);
    }

    public function arrive(ArrivalRequest $request, int $taskId): void
    {
        $sql = 'UPDATE tasks_geoobjects SET trip_type = \'finish\' WHERE tasks_id = '.$taskId.' AND geoobjects_id = '.$request->addressId;

        $this->connection->exec($sql);

        if ($takAddressId = $this->getIdIfLastAddress($taskId, (int)$request->addressId)) {
          $this->setFactArrival($takAddressId, $request->time);
        }
    }

    public function departure(DepartureRequest $request, int $taskId): void
    {
        $sql = 'UPDATE tasks_geoobjects SET trip_type = \'return\' WHERE tasks_id = '.$taskId.' AND geoobjects_id = '.$request->addressId;
        $this->connection->exec($sql);

        if ($takAddressId = $this->getIdIfLastAddress($taskId, (int)$request->addressId)) {
            $this->setFactDeparture($takAddressId, $request->time);
        }
    }

    private function setFactArrival(int $takAddressId, string $time): void
    {
        $sql = "UPDATE tasks_orders SET fact_arrival = '$time' WHERE task_addresses_id = ".$takAddressId;

        $this->connection->exec($sql);
    }

    private function setFactDeparture(int $takAddressId, string $time): void
    {
        $sql = "UPDATE tasks_orders SET fact_departure = '$time' WHERE task_addresses_id = ".$takAddressId;

        $this->connection->exec($sql);
    }

    private function getIdIfLastAddress(int $taskId, int $addressId): ?int
    {
        $sql = 'SELECT id, `order` FROM tasks_geoobjects WHERE  tasks_id = '.$taskId.' AND geoobjects_id = '.$addressId;
        $res = $this->connection->query($sql);
        $row = $res->fetch();
        /*if (isset($row['order'])) {
            $sql = 'SELECT count(*) as countNext FROM tasks_geoobjects WHERE  tasks_id = '.$taskId.' AND geoobjects_id != '.$addressId.'
            AND `order` > '.$row['order'];
            $res2 = $this->connection->query($sql);
            $row2 = $res2->fetch();

            return $row2['countNext'] <= 0 ? $row['id'] : null;
        }*/

        return $row['id'] ?? null;
    }

    public function modify(ModifyRequest $request): int
    {
      $csql =  "SELECT id, ext_id FROM tasks WHERE ext_id = '$request->extId'";
      $cres = $this->connection->query($csql);
      $crow = $cres->fetch();
      $sql = $crow ? 'UPDATE' : 'INSERT INTO';

      $vehicles_id = !is_numeric($request->vehiclesId)
        ? $this->getVehiclesIdByExtId($request->vehiclesId)
        : $request->vehiclesId;

      $sql .= sprintf(
          ' tasks SET vehicles_id =\'%d\', ext_id=\'%s\', status=\'%s\'',
          $vehicles_id,
          $request->extId,
          $request->status
      );
      if ($request->loadedWeight) {
          $sql .= ", loaded_weight = '$request->loadedWeight'";
      }
      if ($request->emptyWeight) {
          $sql .= ", empty_weight = '$request->emptyWeight'";
      }
      if ($request->number) {
          $sql .= ", number = '$request->number'";
      }
      if ($request->comment) {
          $sql .= ", comment = '$request->comment'";
      }
      if ($request->starttime) {
          $sql .= ", starttime = '$request->starttime'";
      }
      if ($request->endtime) {
          $sql .= ", endtime = '$request->endtime'";
      }
      if ($request->updated) {
          $sql .= ", updated = '$request->updated'";
      }
      if ($crow) $sql .= ", deleted = 0 WHERE ext_id = '$request->extId'";

      $this->connection->exec($sql);
      return $crow['id'] ?? (int)$this->connection->lastInsertId();
    }

    public function delete(string $ext_id): int
    {
      $sql = "UPDATE tasks SET deleted = 1 WHERE ext_id = '$ext_id'";
      $this->connection->exec($sql);

      $sql = "SELECT id FROM tasks WHERE ext_id = '$ext_id'";
      $res = $this->connection->query($sql);

      return $res->fetch()['id'];
    }

    public function assign(AssignRequest $request, int $taskId): void
    {
        $sql = "UPDATE tasks SET vehicles_id = $request->vehiclesId, updated = '$request->time' WHERE id = $taskId";

        $this->connection->exec($sql);
    }

    public function status(StatusRequest $request, string $status): void
    {
      $sql = "UPDATE tasks_orders SET status = '$status' WHERE id = $request->orderId";

      $this->connection->exec($sql);
    }

    public function findAddressesByTaskId(int $taskId): ?array
    {
      $sql = sprintf('SELECT id, geoobjects_id, `order`, `trip_type` FROM tasks_geoobjects WHERE tasks_id = %d', $taskId);
      $result = [];

      $res = $this->connection->query($sql);

      while ($row = $res->fetch()) {
        $subSql = sprintf("
SELECT id, ext_id, name, type, address, lat, `long`, radius, contractors_has_geoobjects.contractors_id as contractor_id, deleted 
FROM geoobjects 
LEFT JOIN contractors_has_geoobjects on (geoobjects.id = contractors_has_geoobjects.geoobjects_id)
WHERE id = %d", $row['geoobjects_id']);

        $subRes = $this->connection->query($subSql);

        $addressRow = $subRes->fetch();
        $result[] = new TaskAddress(
          $addressRow['id'],
          $addressRow['ext_id'],
          $addressRow['name'],
          $addressRow['address'],
          $addressRow['lat'],
          $addressRow['long'],
          $addressRow['radius'],
          $addressRow['deleted'],
          $row['order'],
          $addressRow['type'],
          $row['trip_type'],
          $this->findOrdersByTaskAddressesId($row['id']),
          $this->getContractorById($addressRow['contractor_id'])
        );
      }
      return $result;
    }

    public function findOrdersByTaskAddressesId(int $taskAddressesId): ?array
    {
      $sql = sprintf("
SELECT id, task_addresses_id, ext_id, action, volume, weight, gross_weight, 
       package_weight, status, failed_reason, plan_arrival, 
       plan_departure, fact_arrival, fact_departure, payload, comment
FROM tasks_orders 
WHERE task_addresses_id = %d", $taskAddressesId);
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
          $row['comment']
        );
      }

      return $result;
    }

    public function getContractorById(?int $contractorId): ?array
    {
      if ($contractorId == null) {
        return null;
      }
      $sql = sprintf("SELECT id, ext_id, name, code FROM contractors WHERE id = %d", $contractorId);
      $res = $this->connection->query($sql);

      $row = $res->fetch();

      return $row == false ? [] : $row;
    }

    public function getVehiclesIdByExtId(string $id) {
      $sql = "SELECT id FROM vehicles WHERE ext_id='$id'";
      $res = $this->connection->query($sql);

      $row = $res->fetch();

      return $row['id'] ?? false;
    }

    public function getDriverNameByUserId(int $id): ?string
    {
      return $this->connection->query("SELECT fullname FROM acl_user WHERE id = $id")->fetch()['fullname'];
    }

    public function getExtIdById(int $id): string
    {
      return $this->connection->query("SELECT ext_id FROM tasks WHERE id = $id")->fetch()['ext_id'];
    }

    public function getOrderExtIdByOrderId(int $id): string
    {
      return $this->connection->query("SELECT ext_id FROM tasks_orders WHERE id = $id")->fetch()['ext_id'];
    }

    public function setWeight(string $taskExtId, WeightRequest $request): void
    {
      $sql = "UPDATE tasks SET ";
      if ($request->loadedWeight) {
        $sql .= "`loaded_weight` = '$request->loadedWeight', ";
      }

      if ($request->emptyWeight) {
        $sql .= "`empty_weight` = '$request->emptyWeight' ";
      }
      $sql = rtrim($sql, ', ');
      $sql .= " WHERE `ext_id` = '$taskExtId'";
      $this->connection->query($sql);
    }

    public function setTaskOdometer($taskId, $odometer): void
    {
      $sql = "UPDATE tasks SET odometer = '$odometer' WHERE id = '$taskId'";
      $this->connection->query($sql);
    }

    public function setPayload(PayloadRequest $request): void
    {
      $sql = "SELECT payload FROM tasks_orders WHERE id = " . $request->orderId;
      $res = $this->connection->query($sql);
      $payload = [];
      if ($row = $res->fetch()) {
        $payload = json_decode($row['payload'], true);
      }

      if ($request->hopper) {
        $payload['hopper'] = $request->hopper;
      }

      if ($request->replacementHopper) {
        $payload['replacement_hopper'] = $request->replacementHopper;
      }

      $sql = "UPDATE tasks_orders set `payload` = '" . json_encode($payload) . "' WHERE id = " . $request->orderId;

      $this->connection->query($sql);
    }
}