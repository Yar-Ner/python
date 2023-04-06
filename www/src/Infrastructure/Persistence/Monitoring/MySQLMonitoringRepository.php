<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Monitoring;

use App\Domain\Monitoring\Monitoring;
use App\Domain\Monitoring\MonitoringRepository;
use App\Domain\Monitoring\Request\ViewMonitoringRequest;

class MySQLMonitoringRepository implements MonitoringRepository
{
    /**
     * @var \PDO[]
     */
    private $connection;

    public function __construct(\PDO $connection)
    {
        $this->connection = $connection;
    }

    public function findVehicleLocation(?int $vehiclesId): array
    {
        $result = [];

        if ($vehiclesId != 0) {
            $sql = sprintf(
              'SELECT gps_location.id, vehicles_id, `time`, lattitude, longitude, speed, distance, direction, 
                             tasks_id, orders_id, altitude, accuracy, vehicles.name, color
                      FROM gps_location 
                      LEFT JOIN vehicles ON (gps_location.vehicles_id = vehicles.id)
                      WHERE vehicles_id = %d 
                      ORDER BY TIME DESC LIMIT 1',
              $vehiclesId
            );

            $res = $this->connection->query($sql);

            while ($row = $res->fetch()) {
                $monitoringObj = new Monitoring(
                $row['id'],
                $row['vehicles_id'],
                $row['time'],
                $row['lattitude'],
                $row['longitude'],
                $row['speed'],
                $row['distance'],
                $row['tasks_id'],
                $row['orders_id'],
                $row['direction'],
                $row['altitude'],
                $row['accuracy'],
                null
              );
              $monitoringObj->setColor($row['color']);
              $monitoringObj->setName($row['name']);
              $result[] = $monitoringObj;
            }

            return $result;
        } else {
              $sql = 'SELECT MAX(time) as time FROM gps_location group by vehicles_id';
            $res = $this->connection->query($sql);

            while($row = $res->fetch()) {
              $subSql = sprintf(
                'SELECT gps_location.id, vehicles_id, `time`, lattitude, longitude, speed, distance, direction, 
                               tasks_id, orders_id, altitude, accuracy, vehicles.name, color 
                       FROM gps_location 
                       LEFT JOIN vehicles on (gps_location.vehicles_id = vehicles.id)
                       WHERE `time` = \'%s\'', $row['time']);
              $subRes = $this->connection->query($subSql);
              $subRow = $subRes->fetch();

              $monitoringObj = new Monitoring(
                $subRow['id'],
                $subRow['vehicles_id'],
                $subRow['time'],
                $subRow['lattitude'],
                $subRow['longitude'],
                $subRow['speed'],
                $subRow['distance'],
                $subRow['tasks_id'],
                $subRow['orders_id'],
                $subRow['direction'],
                $subRow['altitude'],
                $subRow['accuracy'],
                  null
              );
              $monitoringObj->setColor($subRow['color']);
              $monitoringObj->setName($subRow['name']);
              $result[] = $monitoringObj;
            }

            return $result;
        }
    }

    public function save(Monitoring $monitoring): int
    {
        $sql = 'INSERT INTO gps_location SET';

        $sql .= sprintf(
              ' vehicles_id=\'%d\', 
                time=\'%s\', 
                lattitude=\'%s\', 
                longitude=\'%s\', 
                speed=\'%d\', 
                distance=\'%d\', 
                tasks_id=\'%d\', 
                orders_id=\'%d\', 
                direction=\'%d\', 
                altitude=\'%d\', 
                accuracy=\'%d\'',
              $monitoring->getVehiclesId(),
              $monitoring->getTime(),
              $monitoring->getLatitude(),
              $monitoring->getLongitude(),
              $monitoring->getSpeed(),
              $monitoring->getDistance(),
              $monitoring->getTasksId(),
              $monitoring->getOrdersId(),
              $monitoring->getDirection(),
              $monitoring->getAltitude(),
              $monitoring->getAccuracy()
        );

        $this->connection->exec($sql);

        return (int) $this->connection->lastInsertId();
    }

    public function getTrackByVehicle(int $vehicleId, $request): ?array
    {

      $sql = 'SELECT 
                gps_location.id, gps_location.vehicles_id, `time`, lattitude, longitude, speed, distance, direction, 
                gps_location.tasks_id, gps_location.orders_id,
                altitude, accuracy, vehicles.name, color
              FROM gps_location 
              LEFT JOIN vehicles ON (gps_location.vehicles_id = vehicles.id)
              WHERE vehicles_id = '.$vehicleId;
      if($request) {
        if (isset($request['location'])) {
          $sql .= ' AND gps_location.id = '.$request['location'];
        } else {
          if ($request['tasks_id'] && $request['tasks_id'] != "") {
            $sql .= ' AND tasks_id = ' . $request['tasks_id'];
          }

          if (isset($request['orders_id']) && $request['orders_id'] != "") {
            $sql .= ' AND orders_id = ' . $request['orders_id'];
          }

          if (isset($request['from']) && $request['from'] != "") {
            $sql .= sprintf(' AND time >= \'%s\'', $request['from']);
          }

          if (isset($request['to']) && $request['to'] != "") {
            $sql .= sprintf(' AND time <= \'%s\'', $request['to']);
          }

          $sql .= " ORDER BY `time`";

          if (isset($request['limit']) && $request['limit'] != "") {
            $sql .= ' LIMIT ' . $request['limit'];
          }
        }
      }

      $result = [];
      $res = $this->connection->query($sql);

      while($row = $res->fetch()) {
        $result[] = new Monitoring(
          $row['id'],
          $row['vehicles_id'],
          $row['time'],
          $row['lattitude'],
          $row['longitude'],
          $row['speed'],
          $row['distance'],
          $row['tasks_id'],
          $row['orders_id'],
          $row['direction'],
          $row['altitude'],
          $row['accuracy'],
          $this->getPhotoInfoByLocation($row['id'])
        );
      }

      return $result;
    }

    public function setOrderIdToLocationPoints(int $taskId, int $orderId, int $locationId) {
      $sql = "SELECT time FROM gps_location WHERE id = $locationId";
      $datetime = $this->connection->query($sql)->fetch()['time'];

      $sql = "UPDATE gps_location SET `orders_id` = '$orderId' WHERE (orders_id IS NULL OR orders_id = 0) AND tasks_id = $taskId AND time <= '$datetime'";
      $this->connection->query($sql);
    }

    private function getPhotoInfoByLocation(int $locationId): array
    {
        $sql = "
        SELECT photos.path, tasks_orders.action, JSON_EXTRACT(payload, '$.cargo_type') as cargo_type, address, 
               contractors.name, tasks_orders.fact_arrival, tasks_orders.fact_departure
        FROM photos
        LEFT JOIN tasks_orders ON (photos.orders_id = tasks_orders.id)
        LEFT JOIN tasks_geoobjects ON (tasks_orders.task_addresses_id = tasks_geoobjects.id)
        LEFT JOIN contractors_has_geoobjects ON (tasks_geoobjects.geoobjects_id = contractors_has_geoobjects.geoobjects_id)
        LEFT JOIN contractors ON (contractors_id = contractors.id)
        LEFT JOIN geoobjects ON (tasks_geoobjects.geoobjects_id = geoobjects.id)
        WHERE photos.location_id = $locationId";

        $res = $this->connection->query($sql);
        $photos = [];

        while($row = $res->fetch()) {
            $photos[] = $row;
        }

        return $photos;
    }
}
