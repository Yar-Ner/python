<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\User;

use App\Domain\User\User;
use App\Domain\User\UserNotFoundException;
use App\Domain\User\UserRepository;

class MySQLUserRepository implements UserRepository
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
        $sql = 'SELECT id, name, fullname, description, state, deleted FROM acl_user WHERE deleted=0 or deleted is null';
        $result = [];
        $res = $this->connection->query($sql);

        while ($row = $res->fetch()) {
            $result[] = new User(
                $row['id'],
                $row['name'],
                '',
                $row['fullname'],
                (string)$row['state'],
                $row['description'],
                $this->findGroupsIdByUserId($row['id']),
                $this->findRulesIdByUserId($row['id'])
            );
        }

        return $result;
    }

    public function getById(int $id): User
    {
        $sql = 'SELECT id, name, fullname, description, state FROM acl_user WHERE id=' . $id;
        $res = $this->connection->query($sql);

        if ($row = $res->fetch()) {
            return new User(
                $row['id'],
                $row['name'],
                '',
                $row['fullname'],
                (string)$row['state'],
                $row['description'],
                $this->findGroupsIdByUserId($row['id']),
                $this->findRulesIdByUserId($row['id'])
            );
        }

        throw new UserNotFoundException();
    }

    public function save(User $user): ?array
    {
        $sql = 'INSERT INTO acl_user SET';

        if ($user->getId()) {
            $sql = 'UPDATE acl_user SET';
        } else {
            if ($user->getPassword() === '') {
              return ['res' => false, 'message' => "Вы указали пустой пароль, введите пароль."];
            }
            $sql .= sprintf(' created=\'%s\',',
                (new \DateTime())->format('Y-m-d H:i:s')
            );
        }

        $exist_user_sql = "SELECT id FROM acl_user WHERE name = '{$user->getUsername()}' AND id !='{$user->getId()}'";
        $res = $this->connection->query($exist_user_sql);
        if ($row = $res->fetch()) {
          if (isset($row['id'])) {
            return ['res' => false, 'message' => "Этот логин уже занят, придумайте другой"];
          }
        }

        $sql .= sprintf(
            ' name=\'%s\', fullname=\'%s\', description=\'%s\', state=\'%s\', updated=\'%s\'',
            $user->getUsername(),
            $user->getFullname(),
            $user->getDescription(),
            $user->getStatus(),
            (new \DateTime())->format('Y-m-d H:i:s')
        );
        if ($user->getPassword()) {
          $sql .= sprintf(", password=sha1(md5(concat(md5(md5('%s')), ';Ej>]sjkip')))", $user->getPassword());
        }

        if ($user->getId()) {
            $sql .= sprintf(' WHERE id =\'%d\'', $user->getId());
        }

        $this->connection->exec($sql);

        if (!$user->getId()) {
            $user->setId((int)$this->connection->lastInsertId());
        }

        $this->updateUserGroups($user);
        $this->updateUserRules($user);

        return null;
    }

    public function delete(int $id): void
    {
        $sql = sprintf('UPDATE acl_user SET deleted=1  WHERE id =\'%s\'', $id);

        $this->connection->exec($sql);
    }

    private function findGroupsIdByUserId(int $id): array
    {
        $sql = 'SELECT acl_user_group_id FROM acl_user_group_has_users WHERE acl_user_id=' . $id;
        $result = [];
        $res = $this->connection->query($sql);

        while ($row = $res->fetch()) {
            $result[] = (int)$row['acl_user_group_id'];
        }

        return $result;
    }

    private function findRulesIdByUserId(int $id): array
    {
        $sql = 'SELECT acl_rule_id FROM acl_user_has_rules WHERE acl_user_id=' . $id;
        $result = [];
        $res = $this->connection->query($sql);

        while ($row = $res->fetch()) {
            $result[] = (int)$row['acl_rule_id'];
        }

        return $result;
    }

    private function deleteUserGroupByUserId(int $id): void
    {
        $sql = 'DELETE FROM acl_user_group_has_users WHERE acl_user_id =' . $id;

        $this->connection->exec($sql);
    }

    private function deleteUserRulesByUserId(int $id): void
    {
        $sql = 'DELETE FROM acl_user_has_rules WHERE acl_user_id =' . $id;

        $this->connection->exec($sql);
    }

    private function saveUserGroups(User $user): void
    {
        $sql = 'DELETE FROM acl_user_group_has_users 
WHERE acl_user_id =' . $user->getId() . ' AND acl_user_group_id NOT IN (' . implode(',', $user->getGroupsId()) . ')';

        $this->connection->exec($sql);

        $sql = 'INSERT INTO acl_user_group_has_users (acl_user_id, acl_user_group_id) VALUES ';
        $valuesSql = '';

        foreach ($user->getGroupsId() as $groupId) {
            $valuesSql .= $valuesSql !== '' ? ',' : '';

            $valuesSql .= sprintf('(%d, %d)', $user->getId(), $groupId);
        }

        $sql .= $valuesSql . ' ON DUPLICATE KEY UPDATE acl_user_id=acl_user_id';

        $this->connection->exec($sql);
    }

    private function saveUserRules(User $user): void
    {
        $sql = 'DELETE FROM acl_user_has_rules 
WHERE acl_user_id =' . $user->getId() . ' AND acl_rule_id NOT IN (' . implode(',', $user->getRulesId()) . ')';

        $this->connection->exec($sql);

        $sql = 'INSERT INTO acl_user_has_rules (acl_user_id, acl_rule_id) VALUES ';
        $valuesSql = '';

        foreach ($user->getRulesId() as $ruleId) {
            $valuesSql .= $valuesSql !== '' ? ',' : '';

            $valuesSql .= sprintf('(%d, %d)', $user->getId(), $ruleId);
        }

        $sql .= $valuesSql . ' ON DUPLICATE KEY UPDATE acl_rule_id=acl_rule_id';

        $this->connection->exec($sql);
    }

    private function updateUserGroups(User $user): void
    {
        if ($user->getGroupsId() === []) {
            $this->deleteUserGroupByUserId($user->getId());
        } else {
            $this->saveUserGroups($user);
        }
    }

    private function updateUserRules(User $user): void
    {
        if ($user->getRulesId() === []) {
            $this->deleteUserRulesByUserId($user->getId());
        } else {
            $this->saveUserRules($user);
        }
    }

    public function findByToken(string $token): ?User
    {
        $sql = "SELECT acl_user_id FROM acl_auth_token WHERE token = '" . $token . "'";
        $res = $this->connection->query($sql);

        if ($row = $res->fetch()) {
            return $this->getById($row['acl_user_id']);
        }

        return null;
    }

  /**
   * @throws UserNotFoundException
   */
  public function getIdByLoginAndPassword(string $login, string $password): int
    {
        $sql = "SELECT id FROM acl_user 
WHERE name='" . addslashes($login) . "' AND password = sha1(md5(concat(md5(md5('" . addslashes($password) . "')), ';Ej>]sjkip')))";
        $res = $this->connection->query($sql);

        if ($row = $res->fetch()) {
            return (int)$row['id'];
        }

        throw new UserNotFoundException();
    }
}
