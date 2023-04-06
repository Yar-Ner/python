<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Geo;

use App\Domain\Geo\Geo;
use App\Domain\Geo\GeoNotFoundException;
use App\Domain\Geo\GeoRepository;

class MySQLGeoRepository implements GeoRepository
{
    /**
     * @var \PDO[]
     */
    private $connection;

    public function __construct(\PDO $connection)
    {
        $this->connection = $connection;
    }

    public function findAll($geoIds, int $areas, int $pos = 0, int $count = 50, ?int $onlyCount = 0, array $filters = [], int $short = 0)
    {
        $fsql = ""; //FILTER
        if (count($filters) > 0) {
          foreach ($filters as $key => $value) {
            if (!$value) continue;
            $fsql .= "AND `$key` like '%$value%' ";
          }
        }

        if ($onlyCount) {
          $sql = "SELECT count(*) as count FROM geoobjects WHERE (geoobjects.deleted = 0 OR geoobjects.deleted IS NULL) ";
          if ($fsql !== '') {
            $sql .= $fsql;
          }
          $res = $this->connection->query($sql);
          $row = $res->fetch();

          return $row['count'];
        }

        $sql = "SELECT id, ext_id, name, type, address, lat, `long`, radius, deleted";

        $lsql = ' FROM geoobjects'; //lsql = FROM and LEFT JOIN

        $wsql = ' WHERE (geoobjects.deleted = 0 OR geoobjects.deleted IS NULL) '; //wsql = WHERE

        if ($geoIds) {
          $wsql .= " AND id IN (".implode(',', $geoIds).") ";
        }
        if ($areas) {
          $wsql.= " AND type = 'Площадка' ";
        }
        if ($fsql !== '') {
          $wsql .= $fsql;
        }

        if ($short) {
          $sql = "SELECT id, name";
          $lsql .= ' LEFT JOIN contractors_has_geoobjects as CG ON (CG.geoobjects_id = geoobjects.id) ';
          $wsql .= " AND CG.geoobjects_id IS NOT NULL ";
        }

        $sql .= $lsql . $wsql;

        if ($count) {
          $sql .= " LIMIT $pos, $count";
        }

        $result = [];
        $res = $this->connection->query($sql);

        if ($short) {
          while ($row = $res->fetch()) {
            $result[] = [
              'id' => $row['id'],
              'value' => $row['name']
            ];
          }
          return $result;
        }

        while ($row = $res->fetch()) {
            $result[] = new Geo($row['id'], $row['name'], $row['type'], $row['address'], $row['lat'], $row['long'], $row['radius'], $row['deleted'], $row['ext_id']);
        }

        return $result;
    }

    public function getById(int $id): Geo
    {
        $sql = 'SELECT id, ext_id, name, type, address, lat, `long`, radius, deleted FROM geoobjects WHERE id=' . $id . ' AND (deleted = 0 OR deleted IS NULL)';
        $res = $this->connection->query($sql);

        if ($row = $res->fetch()) {
            return new Geo($row['id'], $row['name'], $row['type'], $row['address'], $row['lat'], $row['long'], $row['radius'], $row['deleted'], $row['ext_id']);
        }

        throw new GeoNotFoundException();
    }

    public function check1cGeoobject(string $ext_id): ?Geo
    {
      $sql = "SELECT id, ext_id, name, type, address, lat, `long`, radius, deleted FROM geoobjects WHERE ext_id='$ext_id'";
      $res = $this->connection->query($sql);

      if ($row = $res->fetch()) {
        return new Geo($row['id'], $row['name'], $row['type'], $row['address'], $row['lat'], $row['long'], $row['radius'], $row['deleted'], $row['ext_id']);
      } else return null;
    }

    public function delete(int $id): void
    {
        $sql = 'UPDATE geoobjects SET deleted=1';

        if ($id) {
            $sql .= sprintf(' WHERE id =\'%d\'', $id);
        }

        $this->connection->exec($sql);
    }

    public function save(Geo $geo): int
    {
        $sql = 'INSERT INTO geoobjects SET';

        if ($geo->getId()) {
            $sql = 'UPDATE geoobjects SET';
        }

        $sql .= sprintf(
            ' name=\'%s\', type=\'%s\', address=\'%s\', lat=\'%f\', `long`=\'%f\', radius=\'%f\', deleted=\'%s\'',
            $geo->getName(),
            $geo->getType(),
            $geo->getAddress(),
            $geo->getLat(),
            $geo->getLong(),
            $geo->getRadius(),
            $geo->getDeleted()
        );

        if ($geo->getExtId()) {
          $sql .= sprintf(', ext_id =\'%s\'', $geo->getExtId());
        }

        if ($geo->getId()) {
            $sql .= sprintf(' WHERE id =\'%d\'', $geo->getId());
        }

        $this->connection->exec($sql);

        return $geo->getId() ?? (int)$this->connection->lastInsertId();
    }
}
