<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Contractor;

use App\Domain\Contractor\Contractor;
use App\Domain\Contractor\ContractorNotFoundException;
use App\Domain\Contractor\ContractorRepository;
use App\Domain\Task\TaskAddress\TaskAddress;

class MySQLContractorRepository implements ContractorRepository
{
    /**
     * @var \PDO[]
     */
    private $connection;

    public function __construct(\PDO $connection)
    {
        $this->connection = $connection;
    }

    public function findAll(int $pos = 0, int $count = 50, ?int $onlyCount = 0, array $filters = [])
    {
        $ljsql = "";
        if (isset($filters['geoobjectsId']) && $filters['geoobjectsId'] !== "") {
          $ljsql .= " LEFT JOIN contractors_has_geoobjects on (contractors.id = contractors_has_geoobjects.contractors_id) \n";
        }

        $fsql = "";
        if (count($filters) > 0) {
          foreach ($filters as $key => $value) {
            if (!$value) continue;
            if ($key === 'geoobjectsId') {
              $fsql .= " AND contractors_has_geoobjects.geoobjects_id IN ($value) ";
              continue;
            }
            $fsql .= "AND `$key` like '%$value%' ";
          }
        }

        if ($onlyCount) {
            $sql = "SELECT count(*) as count FROM contractors \n";
            $sql .= $ljsql;
            $sql .= " WHERE (contractors.deleted=0 or contractors.deleted IS NULL) \n";
            if ($fsql !== '') {
              $sql .= $fsql;
            }
            $res = $this->connection->query($sql);
            $row = $res->fetch();

            return $row['count'];
        }
        $sql = "SELECT id, ext_id, name, code, inn, comment, deleted FROM contractors \n";
        $sql .= $ljsql;
        $sql .= " WHERE (contractors.deleted=0 or contractors.deleted IS NULL) \n";

        if ($fsql !== '') {
          $sql .= $fsql;
        }

        if ($count) {
          $sql .= " LIMIT $pos, $count";
        }
        $result = [];
        $res = $this->connection->query($sql);

        while ($row = $res->fetch()) {
            $result[] = new Contractor(
              $row['id'],
              $row['ext_id'],
              $row['name'],
              $row['code'],
              $row['inn'],
              $row['comment'],
              $this->getAddressByContractorId($row['id']),
              $row['deleted']);
        }

        return $result;
    }


    public function delete(int $id): void
    {
        $sql = 'UPDATE contractors SET deleted=1';

        if ($id) {
            $sql .= sprintf(' WHERE id =\'%d\'', $id);
        }

        $this->connection->exec($sql);
    }

    public function getById(int $id): Contractor
    {
        $sql = 'SELECT id, ext_id, name, code, inn, comment, deleted FROM contractors WHERE id=' . $id . ' AND (deleted=0 or deleted IS NULL)';
        $res = $this->connection->query($sql);


        if ($row = $res->fetch()) {
            return new Contractor(
                $row['id'],
                $row['ext_id'],
                $row['name'],
                $row['code'],
                $row['inn'],
                $row['comment'],
                $this->getAddressByContractorId($row['id']),
                $row['deleted']
            );
        }

        throw new ContractorNotFoundException();
    }

    public function check1cContractor(string $ext_id): ?Contractor
    {
      $sql = "SELECT id, ext_id, name, code, inn, comment, deleted FROM contractors WHERE ext_id='$ext_id'";

      $res = $this->connection->query($sql);

      if ($row = $res->fetch()) {
        return new Contractor(
          $row['id'],
          $row['ext_id'],
          $row['name'],
          $row['code'],
          $row['inn'],
          $row['comment'],
          $this->getAddressByContractorId($row['id']),
          $row['deleted']
        );
      } else return null;
    }

    public function save(Contractor $contractor): int
    {
        $sql = 'INSERT INTO contractors SET';

        if ($contractor->getId()) {
            $sql = 'UPDATE contractors SET';
        }

        $sql .= sprintf(
            ' ext_id=\'%s\', name=\'%s\', inn=\'%s\', deleted=\'%s\'',
            $contractor->getExtId(),
            $contractor->getName(),
            $contractor->getInn(),
            0
        );

        if ($contractor->getCode()) {
            if ($sql) $sql .= ", ";
            $sql .= " code = '".$contractor->getCode()."'";
        }

        if ($contractor->getComment()) {
            if ($sql) $sql .= ", ";
            $sql .= " comment = '".$contractor->getComment()."'";
        }

        if ($contractor->getId()) {
            $sql .= sprintf(' WHERE id =\'%d\'', $contractor->getId());
        }

      try {
        $this->connection->beginTransaction();
        $this->connection->exec($sql);

        if (!$contractor->getId()) {
          $contractor->setId((int)$this->connection->lastInsertId());
        }

        $this->updateContractorAddresses($contractor->getId(), $contractor->getAddresses());

        $this->connection->commit();

        return $contractor->getId() ?: (int)$this->connection->lastInsertId();
      } catch (PDOException $exception) {
        $this->connection->rollBack();
        throw $exception;
      }
    }

    private function deleteContractorAddresses(int $contractorId) {
      $sql = "DELETE FROM contractors_has_geoobjects WHERE `contractors_id` = '$contractorId'";
      $this->connection->query($sql);
    }

    private function updateContractorAddresses(int $contractorId, array $addresses) {
      if ($addresses === []) {
        $this->deleteContractorAddresses($contractorId);
      } else {
        $this->saveContractorAddresses($contractorId, $addresses);
      }
    }

    private function saveContractorAddresses(int $contractorId, array $addresses) {

      $sql = "DELETE FROM contractors_has_geoobjects 
WHERE contractors_id ='$contractorId' AND geoobjects_id NOT IN (" . implode(',', $addresses) . ")";
      $this->connection->query($sql);

      $sql = "INSERT INTO contractors_has_geoobjects (contractors_id, geoobjects_id) VALUES ";
      $valuesSql = '';

      foreach ($addresses as $addressId) {
        $valuesSql .= $valuesSql !== '' ? ',' : '';

        $valuesSql .= sprintf('(%d, %d)', $contractorId, $addressId);
      }

      $sql .= $valuesSql . ' ON DUPLICATE KEY UPDATE geoobjects_id = geoobjects_id';

      $this->connection->query($sql);
    }

    public function createOrUpdateByExtId(Contractor $contractor): int
    {
        $sql = 'INSERT INTO contractors SET';
        $format = ' ext_id=\'%s\', name=\'%s\',inn=\'%s\', comment=\'%s\'';

        if ($contractor->getCode() !== null) {
            $format .= ' ,code=\'%s\'';
        }
        if ($contractor->getDeleted() !== null) {
            $format .= ' ,deleted=\'%s\'';
        }

        $sql .= sprintf(
            $format,
            $contractor->getExtId(),
            $contractor->getName(),
            $contractor->getInn(),
            $contractor->getComment(),
            $contractor->getCode(),
            $contractor->getDeleted() ?: 0
        );

        if ($contractor->getId()) {
            $sql .= sprintf(' WHERE id =\'%d\'', $contractor->getId());
        } else {
            $sql .= sprintf(
                'ON DUPLICATE KEY UPDATE '.$format,
                $contractor->getExtId(),
                $contractor->getName(),
                $contractor->getInn(),
                $contractor->getComment(),
                $contractor->getCode(),
                $contractor->getDeleted() ?: 0
            );
        }

        $result = $this->connection->query($sql);

        return $result->rowCount() > 0 ? 1 : 0;
    }

    public function getAddressByContractorId(int $contractorId): ?array
    {
      $sql = "SELECT geoobjects.id, ext_id, name, address, lat, `long`, radius 
              FROM contractors_has_geoobjects
              LEFT JOIN geoobjects ON (contractors_has_geoobjects.geoobjects_id = geoobjects.id)
              WHERE contractors_id = $contractorId";
      $res = $this->connection->query($sql);

      $addresses = [];
      while ($row = $res->fetch()) {
        $addresses[] = new TaskAddress(
          $row['id'],
          $row['ext_id'],
          $row['name'],
          $row['address'],
          $row['lat'],
          $row['long'],
          $row['radius']
        );
      }

      return $addresses;
    }
}
