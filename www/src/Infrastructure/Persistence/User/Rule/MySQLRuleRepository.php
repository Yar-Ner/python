<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\User\Rule;

use App\Domain\Settings\DefaultSettings;
use App\Domain\User\Rule\Rule;
use App\Domain\User\Rule\RuleNotFoundException;
use App\Domain\User\Rule\RuleRepository;
use App\Domain\User\User;
use App\Domain\User\UserNotFoundException;
use App\Domain\User\UserRepository;

class MySQLRuleRepository implements RuleRepository
{
    /**
     * @var \PDO[]
     */
    private $connection;

    public function __construct(\PDO $connection)
    {
        $this->connection = $connection;
    }

    public function getById(int $id): Rule
    {
        $sql = 'SELECT id, name, description, handle FROM acl_rule WHERE id = '.$id;
        $res = $this->connection->query($sql);
        $row = $res->fetch();

        if ($row) {
            return new Rule($row['id'], $row['name'], $row['handle'], $row['description']);
        }

        throw new RuleNotFoundException();
    }

    public function getHandleByIds(array $ids): array
    {
        if ($ids === []) {
            return [];
        }

        $sql = 'SELECT handle FROM acl_rule WHERE id IN ('.implode(',', $ids).')';
        $res = $this->connection->query($sql);
        $result = [];

        while ($row = $res->fetch()) {
            $result[] = $row['handle'];
        }

        return $result;
    }

    public function save(Rule $rule): void
    {
        $sql = 'INSERT INTO acl_rule SET';

        if ($rule->getId()) {
            $sql = 'UPDATE acl_rule SET';
        }

        $sql .= sprintf(
            ' name=\'%s\', description=\'%s\', handle=\'%s\', updated=\'%s\'',
            $rule->getName(),
            $rule->getDescription(),
            $rule->getHandle(),
            $rule->getUpdated() ? $rule->getUpdated()->format('Y-m-d H:i:s') : (new \DateTime())->format('Y-m-d H:i:s')
        );

        if ($rule->getId()) {
            $sql .= sprintf(' WHERE id =\'%d\'', $rule->getId());
        }


        $this->connection->exec($sql);
    }


    public function delete(int $id): string
    {
        $resGroup = $this->findRulesInGroup($id);
        $resUsers = $this->findRulesInUsers($id);

        if (!$resGroup && !$resUsers) {
            $sql = sprintf('UPDATE acl_rule SET deleted=1  WHERE id =\'%s\'', $id);
        } else {
            return "Право доступа используется в данный момент";
        }

        $this->connection->exec($sql);
        return "Право удалено";
    }

    public function findRulesInGroup(int $id): array
    {
        $sql = 'SELECT acl_rule_id FROM acl_user_group_has_rules WHERE acl_rule_id='.$id;
        $result = [];
        $res = $this->connection->query($sql);

        while ($row = $res->fetch()) {
            $result[] = (int) $row['acl_rule_id'];
        }

        return $result;
    }

    public function findRulesInUsers(int $id): array
    {
        $sql = 'SELECT acl_rule_id FROM acl_user_has_rules WHERE acl_rule_id='.$id;
        $result = [];
        $res = $this->connection->query($sql);

        while ($row = $res->fetch()) {
            $result[] = (int) $row['acl_rule_id'];
        }

        return $result;
    }

    public function deleteById(int $id): void
    {
        // TODO: Implement deleteById() method.
    }


    public function findAll(): array
    {
        $sql = 'SELECT id, name, description, handle FROM acl_rule WHERE deleted=0 or deleted is null';
        $result = [];
        $res = $this->connection->query($sql);

        while ($row = $res->fetch()) {
            $result[] = new Rule($row['id'], $row['name'], $row['handle'], $row['description']);
        }

        return $result;
    }

    public function getHandleById(int $id): string
    {
        $sql = 'SELECT handle FROM acl_rule WHERE id = '.$id;
        $res = $this->connection->query($sql);
        $row = $res->fetch();

        if ($row) {
            return $row['handle'];
        }

        throw new RuleNotFoundException();
    }
}
