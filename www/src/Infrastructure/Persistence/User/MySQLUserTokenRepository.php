<?php


namespace App\Infrastructure\Persistence\User;


use App\Domain\User\User;
use App\Domain\User\UserToken;

class MySQLUserTokenRepository implements \App\Domain\User\UserTokenRepository
{
    /**
     * @var \PDO[]
     */
    private $connection;

    public function __construct(\PDO $connection)
    {
        $this->connection = $connection;
    }

    public function save(UserToken $token): UserToken
    {
        if ($token->getId()) {
            $sql = 'UPDATE acl_auth_token SET';
        } else {
            $sql = 'INSERT INTO acl_auth_token SET';
        }
        $sql .= sprintf(
            ' acl_user_id=\'%s\',
            token=\'%s\',
            pcode=\'%s\',
            version=\'%s\',
            ip=\'%s\',
            hwid=\'%s\',
            issued=\'%s\',
            updated=\'%s\',
            expire=\'%s\'',
            $token->getUserId(),
            $token->getToken(),
            $token->getPcode(),
            $token->getVersion(),
            $token->getIp() ?? '::1',
            $token->getHwid(),
            $token->getIssued()->format('Y-m-d H:i:s'),
            $token->getUpdated()->format('Y-m-d H:i:s'),
            $token->getExpire()->format('Y-m-d H:i:s')
        );
        $this->connection->exec($sql);

        if (!$token->getId()) {
            $token->setId((int) $this->connection->lastInsertId());
        }

        return $token;
    }

    public function delete(UserToken $token): void
    {
        $sql = "DELETE FROM acl_auth_token WHERE token = '{$token->getToken()}' AND acl_user_id = '{$token->getUserId()}'";

        $this->connection->exec($sql);

    }

    public function deleteByStringAndUserId(string $token, int $userId): void
    {
        $sql = "DELETE FROM acl_auth_token WHERE token = '{$token}' AND acl_user_id = '{$userId}'";

        $this->connection->exec($sql);

    }

}