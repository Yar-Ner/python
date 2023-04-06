<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Alarm;

use App\Domain\Alarm\Alarm;
use App\Domain\Alarm\AlarmRepository;
use App\Domain\Alarm\Request\CreateVehicleAlarmRequest;
use App\Domain\Alarm\VehicleAlarm;

class MySQLAlarmRepository implements AlarmRepository
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
        $sql = 'SELECT id FROM alarms WHERE deleted = 0';
        $result = [];
        $res = $this->connection->query($sql);

        while ($row = $res->fetch()) {
            $result[] = $this->getById($row['id']);
        }

        return $result;
    }

  public function findAllAlarmsVehicles(int $id): array
  {
    $sql = '
SELECT 
vehicles_alarms.id, vehicles_id, vehicles_alarms.created, vehicles_alarms.`active`, alarm_id, alarm_text, decision_time, reset_time, tasks_id, push_sent, location_id, photos_id,
alarms.`type`, icon, alarms.`name`, alarms.color,
`number`-- ,
-- fullname
FROM vehicles_alarms 
LEFT JOIN alarms ON (alarms.id = vehicles_alarms.alarm_id)
LEFT JOIN vehicles ON (vehicles.id = vehicles_alarms.vehicles_id)
-- LEFT JOIN acl_user_has_vehicle ON (vehicles.id = acl_user_has_vehicle.vehicle_id)
-- LEFT JOIN acl_user ON (acl_user.id = acl_user_has_vehicle.acl_user_id)
WHERE vehicles_alarms.active = 1';
    if ($id) {
      $sql .= " AND vehicles_alarms.id = ${id}";
    }
    $result = [];
    $res = $this->connection->query($sql);

    while ($row = $res->fetch()) {
      $result[] = new VehicleAlarm(
        $row['id'],
        $row['vehicles_id'],
        $row['created'],
        $row['active'],
        $row['alarm_id'],
        $row['alarm_text'],
        $row['decision_time'],
        $row['reset_time'],
        $row['tasks_id'],
        $row['push_sent'],
        $row['location_id'],
        $row['photos_id'],
        $row['type'],
        $row['icon'],
        $row['name'],
        $row['color'],
        $row['number']
//        $row['fullname']
      );
    }

    return $result;
  }

    public function getById(int $id): Alarm
    {
        $sql = 'SELECT id, `type`, icon, `name`, color, deleted FROM alarms WHERE id = '.$id;
        $res = $this->connection->query($sql);
        $row = $res->fetch();

        return new Alarm(
            $row['id'],
            $row['type'],
            $row['icon'],
            $row['name'],
            $row['color'],
            $row['deleted']
        );
    }

    public function createVehicleAlarm(CreateVehicleAlarmRequest $request): int
    {
        $sql = sprintf('
INSERT INTO vehicles_alarms SET 
vehicles_id = %d, created = NOW(), active = 1, alarm_id = %d, location_id = %d',
        $request->vehicleId,
        $request->alarmId,
        $request->locationId
        );

        if ($request->photoId) {
          $sql .= ", photos_id = '".$request->photoId."'";
        }

        if ($request->comment) {
          $sql .= ", alarm_text = '".$request->comment."'";
        }

        $this->connection->exec($sql);

        return (int) $this->connection->lastInsertId();
    }

    public function changeActiveVehicleAlarmAction(int $id): int
    {
      $sql = 'UPDATE vehicles_alarms SET active = 0 WHERE id = '.$id;
      if ($this->connection->query($sql)) return $id;
    }
}