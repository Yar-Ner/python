<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Vehicle\Containers;

use App\Domain\Vehicle\Containers\VehiclesContainers;
use App\Domain\Vehicle\Containers\VehiclesContainersRepository;
use App\Domain\Vehicle\Types\VehiclesTypes;
use Fig\Http\Message\StatusCodeInterface;

class MySQLVehiclesContainersRepository implements VehiclesContainersRepository
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
        $sql = 'SELECT id, ext_id, name, description, units, volume, dropped_out 
                FROM vehicles_containers 
                WHERE (deleted = 0 or deleted is null) ';
        $result = [];

        $res = $this->connection->query($sql);

        while ($row = $res->fetch()) {
            $result[] = new VehiclesContainers(
                $row['id'],
                $row['ext_id'],
                $row['name'],
                $row['description'],
                $row['units'],
                $row['volume'],
                $row['dropped_out']
            );
        }

        return $result;
    }

    public function getById(int $id): VehiclesContainers
    {
        $sql = 'SELECT id, ext_id, name, description, units, volume, dropped_out 
                FROM vehicles_containers 
                WHERE (deleted = 0 or deleted is null) AND id=' . $id;
        $res = $this->connection->query($sql);
        $row = $res->fetch();

        if ($row) {
            return new VehiclesContainers(
              $row['id'],
              $row['ext_id'],
              $row['name'],
              $row['description'],
              $row['units'],
              $row['volume'],
              $row['dropped_out']
            );
        }

        throw new \DomainException('Vehicle not found.', StatusCodeInterface::STATUS_NOT_FOUND);
    }

    public function getByExtId(string $extId): ?VehiclesContainers
    {
      $sql = "SELECT id, ext_id, name, description, units, volume, dropped_out 
              FROM vehicles_containers 
              WHERE (deleted = 0 or deleted is null) AND ext_id= '" . $extId . "'";
      $res = $this->connection->query($sql);
      $row = $res->fetch();

      if ($row) {
        return new VehiclesContainers(
          $row['id'],
          $row['ext_id'],
          $row['name'],
          $row['description'],
          $row['units'],
          $row['volume'],
          $row['dropped_out']
        );
      } else {
        return null;
      }
    }

    public function save(VehiclesContainers $vehicleContainer): int
    {
        $sql = 'INSERT INTO vehicles_containers SET';

        if ($vehicleContainer->getId()) {
            $sql = 'UPDATE vehicles_containers SET';
        }

        $sql .= sprintf(
            " name='%s' ",
            $vehicleContainer->getName()
        );

        if ($vehicleContainer->getDescription()) {
          $sql .= ", description='" . $vehicleContainer->getDescription() . "'";
        }

        if ($vehicleContainer->getExtId()) {
          $sql .= sprintf(", ext_id = '%s'", $vehicleContainer->getExtId());
        }

        if ($vehicleContainer->getUnits()) {
          $sql .= sprintf(", units = '%s'", $vehicleContainer->getUnits());
        }

        if ($vehicleContainer->getVolume()) {
          $sql .= sprintf(", volume = '%f'", $vehicleContainer->getVolume());
        }

        if ($vehicleContainer->getDroppedOut()) {
          $sql .= sprintf(", dropped_out = '%d'", $vehicleContainer->getDroppedOut());
        }

        if ($vehicleContainer->getId()) {
            $sql .= sprintf(' WHERE id =\'%d\'', $vehicleContainer->getId());
        }

        $this->connection->exec($sql);

        if (!$vehicleContainer->getId()) {
          $vehicleContainer->setId((int)$this->connection->lastInsertId());
        }

        return $vehicleContainer->getId() ?? (int)$this->connection->lastInsertId();
    }

    public function delete(int $id): int
    {
        $sql = "UPDATE vehicles_containers SET deleted = 1 WHERE id = $id";
        $this->connection->exec($sql);
        return $id;
    }
}