<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Vehicle\Types;

use App\Domain\Vehicle\Types\VehiclesTypes;
use App\Domain\Vehicle\Types\VehiclesTypesRepository;
use Fig\Http\Message\StatusCodeInterface;
use PDOException;

class MySQLVehiclesTypesRepository implements VehiclesTypesRepository
{
    /**
     * @var \PDO[]
     */
    private $connection;

    public function __construct(\PDO $connection)
    {
        $this->connection = $connection;
    }

    public function findAll(): array
    {
        $sql = 'SELECT id, ext_id, name, description FROM vehicles_types where (vehicles_types.deleted = 0 or vehicles_types.deleted is null) ';
        $result = [];

        $res = $this->connection->query($sql);

        while ($row = $res->fetch()) {
            $result[] = new VehiclesTypes(
                $row['id'],
                $row['ext_id'],
                $row['name'],
                $row['description'],
                $this->findContainersIdByVehicleTypeId($row['id']),
              0
            );
        }

        return $result;
    }

    public function getById(int $id): VehiclesTypes
    {
        $sql = 'SELECT id, ext_id, name, description FROM vehicles_types WHERE (vehicles_types.deleted = 0 or vehicles_types.deleted is null) AND id=' . $id;
        $res = $this->connection->query($sql);
        $row = $res->fetch();

        if ($row) {
            return new VehiclesTypes(
                $row['id'],
                $row['ext_id'],
                $row['name'],
                $row['description'],
                $this->findContainersIdByVehicleTypeId($row['id']),
                0
            );
        }

        throw new \DomainException('Vehicle not found.', StatusCodeInterface::STATUS_NOT_FOUND);
    }

    public function getByExtId(string $extId): ?VehiclesTypes
    {
      $sql = "SELECT id, ext_id, name, description FROM vehicles_types WHERE (vehicles_types.deleted = 0 or vehicles_types.deleted is null) AND ext_id= '" . $extId . "'";
      $res = $this->connection->query($sql);
      $row = $res->fetch();

      if ($row) {
        return new VehiclesTypes(
          $row['id'],
          $row['ext_id'],
          $row['name'],
          $row['description'],
          $this->findContainersIdByVehicleTypeId($row['id']),
          0
        );
      } else {
        return null;
      }
    }

    public function save(VehiclesTypes $vehicleType): int
    {
        $sql = 'INSERT INTO vehicles_types SET';

        if ($vehicleType->getId()) {
            $sql = 'UPDATE vehicles_types SET';
        }

        $sql .= sprintf(
            ' name=\'%s\'',
            $vehicleType->getName()
        );

        if ($vehicleType->getDescription()) {
          $sql .= ", description='" . $vehicleType->getDescription() . "'";
        }

        if ($vehicleType->getExtId()) {
          $sql .= sprintf(", ext_id = '%s'", $vehicleType->getExtId());
        }

        if ($vehicleType->getId()) {
            $sql .= sprintf(' WHERE id =\'%d\'', $vehicleType->getId());
        }

        try {
            $this->connection->exec($sql);

            if (!$vehicleType->getId()) {
              $vehicleType->setId((int)$this->connection->lastInsertId());
            }

            $this->updateVehicleTypesContainers($vehicleType);

            return $vehicleType->getId() ?? (int)$this->connection->lastInsertId();
        } catch (PDOException $exception) {
            $this->connection->rollBack();
            throw $exception;
        }
    }

    public function delete(int $id): int
    {
        $sql = "UPDATE vehicles_types SET deleted = 1 WHERE id = $id";
        $this->connection->exec($sql);
        return $id;
    }

    private function findContainersIdByVehicleTypeId(int $id): array
    {
      $sql = 'SELECT container_id FROM vehicle_types_has_containers WHERE vehicle_types_id=' . $id;
      $result = [];
      $res = $this->connection->query($sql);

      while ($row = $res->fetch()) {
        $result[] = (int)$row['container_id'];
      }

      return $result;
    }

    private function deleteContainersByVehicleTypesId(int $id): void
    {
      $sql = 'DELETE FROM vehicle_types_has_containers WHERE vehicle_types_id =' . $id;

      $this->connection->exec($sql);
    }

    private function saveVehicleTypesContainers(VehiclesTypes $vehicleType): void
    {
      $sql = 'DELETE FROM vehicle_types_has_containers 
    WHERE vehicle_types_id =' . $vehicleType->getId() . ' AND container_id NOT IN (' . implode(',', $vehicleType->getContainersId()) . ')';

      $this->connection->exec($sql);

      $sql = 'INSERT INTO vehicle_types_has_containers (vehicle_types_id, container_id) VALUES ';
      $valuesSql = '';

      foreach ($vehicleType->getContainersId() as $containerId) {
        $valuesSql .= $valuesSql !== '' ? ',' : '';

        $valuesSql .= sprintf('(%d, %d)', $vehicleType->getId(), $containerId);
      }

      $sql .= $valuesSql . ' ON DUPLICATE KEY UPDATE container_id=container_id';

      $this->connection->exec($sql);
    }

    private function updateVehicleTypesContainers(VehiclesTypes $vehicleType): void
    {
      if ($vehicleType->getContainersId() === []) {
        $this->deleteContainersByVehicleTypesId($vehicleType->getId());
      } else {
        $this->saveVehicleTypesContainers($vehicleType);
      }
    }
}