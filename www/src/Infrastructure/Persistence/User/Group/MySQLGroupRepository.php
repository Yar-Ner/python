<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\User\Group;

use App\Domain\User\Group\Group;
use App\Domain\User\Group\GroupRepository;
use App\Domain\User\Rule\Rule;
use Slim\Exception\HttpNotFoundException;

class MySQLGroupRepository implements GroupRepository
{
    /**
     * @var \PDO[]
     */
    private $connection;

    public function __construct(\PDO $connection)
    {
        $this->connection = $connection;
    }

    public function getById(int $id): Group
    {
        $sql = 'SELECT id, name, description FROM acl_user_group WHERE id='.$id;
        $res = $this->connection->query($sql);
        $row = $res->fetch();

        if ($row) {
            return new Group(
                $row['id'],
                $row['name'],
                $row['description'],
                $this->findRulesIdByGroupId($row['id']),
                $this->findUsersIdByGroupId($row['id'])
            );
        }

        throw new HttpNotFoundException();
    }

    public function save(Group $group): void
    {
        $sql = 'INSERT INTO acl_user_group SET';

        if ($group->getId()) {
            $sql = 'UPDATE acl_user_group SET';
        }

        $sql .= sprintf(' name=\'%s\', description=\'%s\'', $group->getName(), $group->getDescription());

        if ($group->getId()) {
            $sql .= sprintf(' WHERE id =\'%d\'', $group->getId());
        }

        $this->connection->exec($sql);

        if (!$group->getId()) {
            $group->setId((int) $this->connection->lastInsertId());
        }

        $this->updateGroupRules($group);
        $this->updateGroupUsers($group);
    }

    private function deleteRulesGroupByGroupId(int $id): void
    {
        $sql = 'DELETE FROM acl_user_group_has_rules WHERE acl_user_group_id ='.$id;

        $this->connection->exec($sql);
    }

    private function deleteUsersGroupByGroupId(int $id): void
    {
        $sql = 'DELETE FROM acl_user_group_has_users WHERE acl_user_group_id ='.$id;

        $this->connection->exec($sql);
    }

    private function updateRulesGroup(Group $group): void
    {
        $sql = 'DELETE FROM acl_user_group_has_rules 
WHERE acl_user_group_id ='.$group->getId().' AND acl_rule_id NOT IN ('.implode(',', $group->getRulesId()).')';

        $this->connection->exec($sql);

        $sql = 'INSERT INTO acl_user_group_has_rules (acl_user_group_id, acl_rule_id) VALUES ';
        $valuesSql = '';

        foreach ($group->getRulesId() as $ruleId) {
            $valuesSql .= $valuesSql !== '' ? ',' : '';

            $valuesSql .= sprintf('(%d, %d)', $group->getId(), $ruleId);
        }

        $sql .= $valuesSql.' ON DUPLICATE KEY UPDATE acl_user_group_id=acl_user_group_id';

        $this->connection->exec($sql);
    }

    private function updateUsersGroup(Group $group): void
    {
        $sql = 'DELETE FROM acl_user_group_has_users 
WHERE acl_user_group_id ='.$group->getId().' AND acl_user_id NOT IN ('.implode(',', $group->getUsersId()).')';

        $this->connection->exec($sql);

        $sql = 'INSERT INTO acl_user_group_has_users (acl_user_id, acl_user_group_id) VALUES ';
        $valuesSql = '';

        foreach ($group->getUsersId() as $userId) {
            $valuesSql .= $valuesSql !== '' ? ',' : '';

            $valuesSql .= sprintf('(%d, %d)', $userId, $group->getId());
        }

        $sql .= $valuesSql.' ON DUPLICATE KEY UPDATE acl_user_group_id=acl_user_group_id';

        $this->connection->exec($sql);
    }

    private function updateGroupRules(Group $group): void
    {
        if ($group->getRulesId() === []) {
            $this->deleteRulesGroupByGroupId($group->getId());
        } else {
            $this->updateRulesGroup($group);
        }
    }

    private function updateGroupUsers(Group $group): void
    {
        if ($group->getUsersId() === []) {
            $this->deleteUsersGroupByGroupId($group->getId());
        } else {
            $this->updateUsersGroup($group);
        }
    }

    public function delete(int $id): string
    {
        $resUsers = $this->findGroupInUsers($id);

        if (!$resUsers) {
            $sql = sprintf('UPDATE acl_user_group SET deleted=1  WHERE id =\'%s\'', $id);
        } else {
            return "Группа используется в данный момент";
        }

        $this->connection->exec($sql);
        return "Группа удалена";
    }

    public function findGroupInUsers(int $id): array
    {
        $sql = 'SELECT acl_user_group_id FROM acl_user_group_has_users WHERE acl_user_group_id='.$id;
        $result = [];
        $res = $this->connection->query($sql);

        while ($row = $res->fetch()) {
            $result[] = (int) $row['acl_user_group_id'];
        }

        return $result;
    }

    public function deleteById(int $id): void
    {
        // TODO: Implement deleteById() method.
    }

    public function findAll(): array
    {
        $sql = 'SELECT id, name, description, deleted FROM acl_user_group WHERE deleted=0 or deleted is null';
        $result = [];
        $res = $this->connection->query($sql);

        while ($row = $res->fetch()) {
            $result[] = new Group(
                $row['id'],
                $row['name'],
                $row['description'],
                $this->findRulesIdByGroupId($row['id']),
                $this->findUsersIdByGroupId($row['id'])
            );
        }

        return $result;
    }

    private function findRulesIdByGroupId(int $id): array
    {
        $sql = 'SELECT acl_rule_id FROM acl_user_group_has_rules WHERE acl_user_group_id='.$id;
        $result = [];
        $res = $this->connection->query($sql);

        while ($row = $res->fetch()) {
            $result[] = (int) $row['acl_rule_id'];
        }

        return $result;
    }

    private function findUsersIdByGroupId(int $id): array
    {
        $sql = 'SELECT acl_user_id FROM acl_user_group_has_users WHERE acl_user_group_id='.$id;
        $result = [];
        $res = $this->connection->query($sql);

        while ($row = $res->fetch()) {
            $result[] = (int) $row['acl_user_id'];
        }

        return $result;
    }
}
