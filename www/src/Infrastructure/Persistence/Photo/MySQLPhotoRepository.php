<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Photo;


use App\Domain\Photo\Photo;
use App\Domain\Photo\PhotoRepositoryInterface;
use App\Domain\Photo\Request\ListPhotoRequest;

class MySQLPhotoRepository implements PhotoRepositoryInterface
{
    /**
     * @var \PDO
     */
    private $connection;

    public function __construct(\PDO $connection)
    {
        $this->connection = $connection;
    }

    public function save(Photo $photo): int
    {
        $sql = sprintf(
            'INSERT INTO photos SET name = \'%s\', path = \'%s\', acl_user_id = \'%d\', uploaded = NOW()',
            $photo->getName(),
            $photo->getPath(),
            $photo->getUserId()
        );

        if ($photo->getVehiclesId()) {
            $sql = sprintf('%s, vehicles_id = \'%d\'', $sql, $photo->getVehiclesId());
        }
        if ($photo->getOrderId()) {
            $sql = sprintf('%s, orders_id = \'%d\'', $sql, $photo->getOrderId());
        }
        if ($photo->getAlarmId()) {
            $sql = sprintf('%s, alarms_id = \'%d\'', $sql, $photo->getAlarmId());
        }
        if ($photo->getLocationId()) {
            $sql = sprintf('%s, location_id = \'%d\'', $sql, $photo->getLocationId());
        }

        $this->connection->exec($sql);

        return (int) $this->connection->lastInsertId();
    }

    public function find($request): array
    {
        $result = [];
        $sql = sprintf('SELECT id FROM photos WHERE orders_id  =  \'%d\'', $request['orders_id']);

        if (isset($request['alarms_id'])) {
            $sql = sprintf('%s AND alarms_id = \'%d\'', $sql, $request['alarms_id']);
        }
        if (isset($request['vehicles_id'])) {
            $sql = sprintf('%s AND vehicles_id = \'%d\'', $sql, $request['vehicles_id']);
        }

        $res = $this->connection->query($sql);

        while ($row = $res->fetch()) {
            $result[] = $this->getById($row['id']);
        }

        return $result;
    }

    private function getById($id): Photo
    {
        $sql = sprintf(
            'SELECT id, name, path, acl_user_id, vehicles_id, orders_id, alarms_id, location_id, uploaded FROM photos WHERE id  =  \'%d\'',
            $id
        );

        $res = $this->connection->query($sql);
        $row = $res->fetch();

        return new Photo(
            $row['id'],
            $row['path'],
            $row['name'],
            $row['acl_user_id'],
            $row['vehicles_id'],
            $row['orders_id'],
            $row['alarms_id'],
            $row['location_id'],
            $row['uploaded']
        );
    }
}