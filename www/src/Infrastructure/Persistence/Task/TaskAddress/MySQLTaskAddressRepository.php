<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Task\TaskAddress;

use App\Domain\Task\TaskAddress\TaskAddressRepository;
use App\Domain\Task\TaskOrder\TaskOrderRepository;

class MySQLTaskAddressRepository implements TaskAddressRepository
{
    /**
     * @var \PDO[]
     */
    private $connection;

    public function __construct(\PDO $connection)
    {
        $this->connection = $connection;
    }

      public function modifyArray(int $taskId, ?array $addressRequests, TaskOrderRepository $taskOrderRepository, ?int $geoRadius = 15): void
    {
        $this->clearTaskAddress($taskId);
        if ($addressRequests) {
          array_walk($addressRequests, function (array $request) use ($taskId, $taskOrderRepository, $geoRadius) {
            if ($request['radius'] == 0) $request['radius'] = $geoRadius;
            $addressId = $this->modifyGlobalAddress($request);
            $taId = $this->addAddress($addressId, $request, $taskId);

            if (isset($request['orders'])) {
                $taskOrderRepository->modifyArray($request['orders'], $taId);
            }
          });
        }
    }

    private function modifyGlobalAddress(array $request): int
    {
        $csql = "SELECT id, ext_id FROM geoobjects WHERE ext_id = '".$request['ext_id']."'";
        $cres = $this->connection->query($csql);
        $crow = $cres->fetch();
        $sql = $crow ? "UPDATE geoobjects" : "INSERT INTO geoobjects";

        $sql .= sprintf('
        SET ext_id = \'%s\', name = \'%s\', type = \'%s\', address = \'%s\'',
        $request['ext_id'],
        $request['name'],
        $request['type'],
        $request['address']);
        if (isset($request['lat']) && $request['lat'] != '') {
          $sql .= sprintf(', `lat` = \'%s\'', $request['lat']);
        }
        if (isset($request['long']) && $request['long'] != '') {
          $sql .= sprintf(', `long` = \'%s\'', $request['long']);
        }
        if (isset($request['radius']) && $request['radius'] != '') {
          $sql .= sprintf(', `radius` = \'%s\'', $request['radius']);
        }
        if ($crow) $sql .= sprintf(' WHERE ext_id = \'%s\'', $request['ext_id']);
        $this->connection->exec($sql);
        $addressId = $crow ? $crow['id'] : (int)$this->connection->lastInsertId();

        if (isset($request['contractor']) && $request['contractor'] != '') {
          $this->updateContractorAddress($request['contractor'], $addressId);
        }

        return $addressId;
    }

    private function modify(array $request, int $taskAddressId): int
    {
        $sql = sprintf("UPDATE tasks_ge SET `type` = '%s'", $request['type']);

        if ($request['order']) {
          $sql .= sprintf(", `order` = %d", $request['order']);
        }

        $sql .= " WHERE id = $taskAddressId";

        $this->connection->exec($sql);
        return $taskAddressId;
    }

    private function addAddress(int $addressId, array $request, int $taskId): int
    {
        $sql = sprintf(
            'INSERT INTO tasks_geoobjects SET geoobjects_id = %d, tasks_id = %d',
            $addressId,
            $taskId
        );
        if (isset($request['trip_type'])) {
            $sql .= ", `trip_type` = '".$request['trip_type']."'";
        }
        if (isset($request['order'])) {
          $sql .= ", `order` = '".$request['order']."'";
        }

        $this->connection->exec($sql);
        return (int) $this->connection->lastInsertId();
    }

    public function clearTaskAddress(int $taskId) {
      $sql = "DELETE FROM tasks_geoobjects WHERE tasks_id = ".$taskId;
      $this->connection->exec($sql);
    }

    private function getTaskAddressIdIfExist(int $addressId, int $taskId): ?int
    {
        $sql = 'SELECT id FROM tasks_geoobjects WHERE tasks_id = '.$taskId.' AND address_id = '.$addressId;
        $res = $this->connection->query($sql);
        $row = $res->fetch();

        return $row['id'] ?? null;
    }

  private function getCreateContractor($contractor): int
  {
    $sql = "SELECT id FROM contractors WHERE ext_id = '".$contractor['ext_id']."'";

    $res = $this->connection->query($sql);
    $row = $res->fetch();

    if(!$row) {
      $isql = "INSERT INTO contractors SET `ext_id` = '".$contractor['ext_id']."'";
      if (isset($contractor['name']) && $contractor['name'] != '') {
        $isql .= sprintf(', `name` = \'%s\'', $contractor['name']);
      }
      if (isset($contractor['code']) && $contractor['code'] != '') {
        $isql .= sprintf(', `code` = \'%s\'', $contractor['code']);
      }

      $this->connection->exec($isql);
    }
    return $row ? $row['id'] : (int)$this->connection->lastInsertId();
  }

  private function updateContractorAddress($contractor, int $addressId): void
  {
      $id = $this->getCreateContractor($contractor);
      $this->saveContractorAddress($id, $addressId);
  }

  private function saveContractorAddress(int $contractorId, int $addressId): void
  {
    $sql = " DELETE FROM contractors_has_geoobjects WHERE geoobjects_id = $addressId;
             INSERT INTO contractors_has_geoobjects SET geoobjects_id = $addressId, contractors_id = $contractorId";

    $this->connection->exec($sql);
  }
}