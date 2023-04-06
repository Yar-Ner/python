<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Device;

use App\Domain\Device\Device;
use App\Domain\Device\DeviceNotFoundException;
use App\Domain\Device\DeviceRepository;
use App\Domain\User\User;

class MySQLDeviceRepository implements DeviceRepository
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
        $sql = 'SELECT id, name, imei, deleted FROM devices WHERE (devices.deleted = 0 or devices.deleted is null) ';
        $result = [];
        $res = $this->connection->query($sql);

        while ($row = $res->fetch()) {
            $result[] = new Device(
                $row['id'],
                $row['name'],
                $row['imei'],
                $this->findVehicleIdByDeviceId($row['id'])
            );
        }

        return $result;
    }

    public function getById(int $id): Device
    {
        $sql = 'SELECT id, name, imei, deleted FROM devices WHERE (deleted = 0 or deleted is null) and id=' . $id;
        $res = $this->connection->query($sql);
        $row = $res->fetch();

        if ($row) {
            return new Device(
                $row['id'],
                $row['name'],
                $row['imei'],
                $this->findVehicleIdByDeviceId($row['id'])
            );
        }

        throw new DeviceNotFoundException();
    }

    public function save(Device $device): void
    {
        $sql = 'INSERT INTO devices SET';

        if ($device->getId()) {
            $sql = 'UPDATE devices SET';
        }

        $sql .= sprintf(
            ' name=\'%s\', imei=\'%s\'',
            $device->getName(),
            $device->getImei()
        );

        if ($device->getId()) {
            $sql .= sprintf(' WHERE id =\'%d\'', $device->getId());
        }

        try {
            $this->connection->beginTransaction();
            $this->connection->exec($sql);

            if (!$device->getId()) {
                $device->setId((int)$this->connection->lastInsertId());
            }

            $this->updateDeviceVehicle($device);
            $this->connection->commit();
        } catch (\PDOException $exception) {
            $this->connection->rollBack();
            throw $exception;
        }
    }

    public function delete(int $id): void
    {
        $sql = sprintf('UPDATE devices SET deleted=1  WHERE id =\'%s\'', $id);

        $this->connection->exec($sql);
    }

    private function updateDeviceVehicle(Device $device): void
    {
        if ($device->getVehicleId() === []) {
            $this->deleteVehicleByDeviceId($device->getId());
        } else {
            $this->saveDeviceVehicle($device);
        }
    }

    private function deleteVehicleByDeviceId(int $id): void
    {
        $sql = 'DELETE FROM devices_has_vehicles WHERE devices_id =' . $id;

        $this->connection->exec($sql);
    }

    private function saveDeviceVehicle(Device $device): void
    {
        $sql = 'DELETE FROM devices_has_vehicles 
WHERE devices_id =' . $device->getId() . ' AND vehicles_id NOT IN (' . implode(',', $device->getVehicleId()) . ')';

        $this->connection->exec($sql);

        $sql = 'INSERT INTO devices_has_vehicles (devices_id, vehicles_id) VALUES ';
        $valuesSql = '';

        foreach ($device->getVehicleId() as $vehicleId) {
            $valuesSql .= $valuesSql !== '' ? ',' : '';

            $valuesSql .= sprintf('(%d, %d)', $device->getId(), $vehicleId);
        }

        $sql .= $valuesSql . ' ON DUPLICATE KEY UPDATE vehicles_id=vehicles_id';

        $this->connection->exec($sql);
    }

    private function findVehicleIdByDeviceId(int $id): array
    {
        $sql = 'SELECT vehicles_id FROM devices_has_vehicles WHERE devices_id=' . $id;
        $result = [];
        $res = $this->connection->query($sql);

        while ($row = $res->fetch()) {
            $result[] = (int)$row['vehicles_id'];
        }

        return $result;
    }

    public function findByUser(User $user): array
    {
        $sql = 'SELECT devices_id as id FROM devices_has_user WHERE acl_user_id='.$user->getId();
        $res = $this->connection->query($sql);
        $result = [];

        while ($row = $res->fetch()) {
            try {
                $result[] = $this->getById($row['id']);
            } catch (DeviceNotFoundException $exception) {}
        }

        return $result;
    }
}
