<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Vehicle;

use App\Domain\Vehicle\Vehicle;
use App\Domain\Vehicle\VehicleRepository;
use Fig\Http\Message\StatusCodeInterface;
use PDOException;

class MySQLVehiclesRepository implements VehicleRepository
{
    /**
     * @var \PDO[]
     */
    private $connection;

    public function __construct(\PDO $connection)
    {
        $this->connection = $connection;
    }

    public function findAll(array $userRules): array
    {
        $sql = 'SELECT id, ext_id, name, number, type, description, color, weight, active, odometer, deleted 
                FROM vehicles 
                WHERE deleted = 0 OR deleted IS NULL';
        $result = [];
        if (!in_array(8, $userRules)) {
          $sql .= " AND active = 1";
        }
        $res = $this->connection->query($sql);

        while ($row = $res->fetch()) {
            $result[] = new Vehicle(
                $row['id'],
                $row['name'],
                $row['number'],
                (int)$row['type'],
                $this->findContainersByTypeId((int)$row['type']),
                $row['description'],
                $this->findUsersIdByVehicleId($row['id']),
                $this->findDevicesIdByVehicleId($row['id']),
                $row['ext_id'],
                $row['color'],
                $row['weight'],
                $row['active'],
                (int)$row['odometer']
            );
        }

        return $result;
    }

    public function getById(int $id, array $userRules = [])
    {
        $sql = 'SELECT id, ext_id, name, number, type, description, color, weight, active, odometer, deleted FROM vehicles WHERE (deleted = 0 OR deleted IS NULL) AND id=' . $id;

        if (!in_array(8, $userRules)) {
          $sql .= " AND active = 1";
        }

        $res = $this->connection->query($sql);
        $row = $res->fetch();

        if ($row) {
            return new Vehicle(
                $row['id'],
                $row['name'],
                $row['number'],
                (int)$row['type'],
                $this->findContainersByTypeId((int)$row['type']),
                $row['description'],
                $this->findUsersIdByVehicleId($row['id']),
                $this->findDevicesIdByVehicleId($row['id']),
                $row['ext_id'],
                $row['color'],
                $row['weight'],
                $row['active'],
                $row['odometer']
            );
        } else {
          return null;
        }
    }

    public function check1cVehicle(string $ext_id): ?Vehicle
    {
        $sql = "SELECT id, ext_id, name, number, type, description, color, weight, active, odometer, deleted FROM vehicles where ext_id = '$ext_id'";
        $res = $this->connection->query($sql);
        $row = $res->fetch();

        if ($row) {
          return new Vehicle(
            $row['id'],
            $row['name'],
            $row['number'],
            (int)$row['type'],
            $this->findContainersByTypeId((int)$row['type']),
            $row['description'],
            $this->findUsersIdByVehicleId($row['id']),
            $this->findDevicesIdByVehicleId($row['id']),
            $row['ext_id'],
            $row['color'],
            $row['weight'],
            $row['active'],
            $row['odometer']
          );
        } else return null;
    }

    public function save(Vehicle $vehicle): int
    {
        $sql = 'INSERT INTO vehicles SET';

        if ($vehicle->getId()) {
            $sql = 'UPDATE vehicles SET';
        }

        $sql .= sprintf(
            ' name=\'%s\', number=\'%s\', description=\'%s\'',
            $vehicle->getName(),
            $vehicle->getNumber(),
            $vehicle->getDescription()
        );
        if ($vehicle->getColor() && $vehicle->getColor() != '') {
          $sql .= ", color='".$vehicle->getColor()."'";
        } else {
          $sql .= ", color='".'#'.str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT)."'";
        }

        if ($vehicle->getType() !== null) {
          $sql .= ", type='".$vehicle->getType()."'";
        }

        if ($vehicle->getWeight() !== null) {
          $sql .= ", weight='".$vehicle->getWeight()."'";
        }

        if ($vehicle->getActive() !== null) {
          $sql .= ", active='".$vehicle->getActive()."'";
        } else {
          $sql .= ", active='1'";
        }

        if ($vehicle->getOdometer() !== null) {
          $sql .= ", `odometer` ='".$vehicle->getOdometer()."'";
        }

        if ($vehicle->getExtId() && $vehicle->getExtId() != '') {
          $sql .= ", ext_id='".$vehicle->getExtId()."'";
        }

        if ($vehicle->getId()) {
            $sql .= sprintf(' WHERE id =\'%d\'', $vehicle->getId());
        }

        try {
            $this->connection->beginTransaction();
            $this->connection->exec($sql);

            if (!$vehicle->getId()) {
                $vehicle->setId((int)$this->connection->lastInsertId());
            }

            $this->updateVehicleUsers($vehicle);
            $this->updateVehicleDevices($vehicle);
            $this->connection->commit();

            return $vehicle->getId() ?? (int)$this->connection->lastInsertId();
        } catch (PDOException $exception) {
            $this->connection->rollBack();
            throw $exception;
        }
    }

    public function delete(int $id): void
    {
        $sql = sprintf('UPDATE vehicles SET deleted = 1 WHERE id =\'%s\'', $id);
        $this->deleteUsersByVehicleId($id);

        $this->connection->exec($sql);

    }

    private function updateVehicleUsers(Vehicle $vehicle): void
    {
        if ($vehicle->getUsersId() === []) {
            $this->deleteUsersByVehicleId($vehicle->getId());
        } else {
            $this->saveVehicleUsers($vehicle);
        }
    }

    private function updateVehicleDevices(Vehicle $vehicle): void
    {
        if ($vehicle->getDevicesId() === []) {
            $this->deleteDevicesByVehicleId($vehicle->getId());
        } else {
            $this->saveVehicleDevices($vehicle);
        }
    }

    private function deleteUsersByVehicleId(int $id): void
    {
        $sql = 'DELETE FROM acl_user_has_vehicle WHERE vehicle_id =' . $id;

        $this->connection->exec($sql);
    }

    private function deleteDevicesByVehicleId(int $id): void
    {
        $sql = 'DELETE FROM devices_has_vehicles WHERE vehicles_id =' . $id;

        $this->connection->exec($sql);
    }

    private function saveVehicleUsers(Vehicle $vehicle): void
    {
        $sql = 'DELETE FROM acl_user_has_vehicle 
WHERE vehicle_id =' . $vehicle->getId() . ' AND acl_user_id NOT IN (' . implode(',', $vehicle->getUsersId()) . ')';

        $this->connection->exec($sql);

        $sql = 'INSERT INTO acl_user_has_vehicle (vehicle_id, acl_user_id) VALUES ';
        $valuesSql = '';

        foreach ($vehicle->getUsersId() as $userId) {
            $valuesSql .= $valuesSql !== '' ? ',' : '';

            $valuesSql .= sprintf('(%d, %d)', $vehicle->getId(), $userId);
        }

        $sql .= $valuesSql . ' ON DUPLICATE KEY UPDATE acl_user_id=acl_user_id';

        $this->connection->exec($sql);
    }

    private function saveVehicleDevices(Vehicle $vehicle): void
    {
        $sql = 'DELETE FROM devices_has_vehicles 
WHERE vehicles_id =' . $vehicle->getId() . ' AND devices_id NOT IN (' . implode(',', $vehicle->getDevicesId()) . ')';

        $this->connection->exec($sql);

        $sql = 'INSERT INTO devices_has_vehicles (vehicles_id, devices_id) VALUES ';
        $valuesSql = '';

        foreach ($vehicle->getDevicesId() as $deviceId) {
            $valuesSql .= $valuesSql !== '' ? ',' : '';

            $valuesSql .= sprintf('(%d, %d)', $vehicle->getId(), $deviceId);
        }

        $sql .= $valuesSql . ' ON DUPLICATE KEY UPDATE devices_id=devices_id';

        $this->connection->exec($sql);
    }

    private function findUsersIdByVehicleId(int $id): array
    {
        $sql = 'SELECT acl_user_id FROM acl_user_has_vehicle WHERE vehicle_id=' . $id;
        $result = [];
        $res = $this->connection->query($sql);

        while ($row = $res->fetch()) {
            $result[] = (int)$row['acl_user_id'];
        }

        return $result;
    }

    private function findDevicesIdByVehicleId(int $id): array
    {
        $sql = 'SELECT devices_id FROM devices_has_vehicles WHERE vehicles_id=' . $id;
        $result = [];
        $res = $this->connection->query($sql);

        while ($row = $res->fetch()) {
            $result[] = (int)$row['devices_id'];
        }

        return $result;
    }

    public function findAllByUserId(int $id, array $userRules): array
    {
        $sql = 'SELECT vehicle_id FROM acl_user_has_vehicle WHERE acl_user_id=' . $id;
        $result = [];
        $res = $this->connection->query($sql);

        while ($row = $res->fetch()) {
            $vehicle = $this->getById($row['vehicle_id'], $userRules);
            if ($vehicle) $result[] = $vehicle;
        }

        return $result;
    }

    private function findContainersByTypeId(int $typeId): ?array
    {
      $sql = "    SELECT container_id, `name` 
                   FROM vehicle_types_has_containers
                   LEFT JOIN vehicles_containers ON (vehicle_types_has_containers.container_id = vehicles_containers.id)
                   WHERE vehicle_types_id = $typeId AND (deleted = 0 OR deleted is null)";
      $res = $this->connection->query($sql);
      $containers = [];
      while ($crow = $res->fetch()) {
        $containers[] = [
          'id' => $crow['container_id'],
          'name' => $crow['name']
        ];
      }

      return $containers;
    }
}