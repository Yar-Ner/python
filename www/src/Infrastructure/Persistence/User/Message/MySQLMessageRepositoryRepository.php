<?php

declare(strict_types=1);


namespace App\Infrastructure\Persistence\User\Message;


use App\Domain\User\Message\Message;
use App\Domain\User\Message\MessageRepositoryInterface;

class MySQLMessageRepositoryRepository implements MessageRepositoryInterface
{
    /**
     * @var \PDO
     */
    private $connection;

    public function __construct(\PDO $connection)
    {
        $this->connection = $connection;
    }

    public function getMessages(int $toUserId, ?string $fromDate, ?string $toDate): array
    {
        $result = [];
        $sql = sprintf('SELECT id FROM chat_message WHERE `to` = %d', $toUserId);

        if ($fromDate) {
            $sql = sprintf('%s AND sent >= \'%s\' ',$sql, $fromDate);
        }
        if ($toDate) {
            $sql = sprintf('%s AND sent <= \'%s\' ',$sql, $toDate);
        }

        $res = $this->connection->query($sql);

        while ($row = $res->fetch()) {
            $result[] = $this->getById($row['id']);
        }

        return $result;
    }

    public function markAsDelivered(array $ids): void
    {
        $sql = 'UPDATE chat_message SET delivered = NOW() WHERE id IN ('.implode(',', $ids).')';

        $this->connection->exec($sql);
    }

    public function markAsRead(array $ids): void
    {
        $sql = 'UPDATE chat_message SET readed = NOW() WHERE id IN ('.implode(',', $ids).')';

        $this->connection->exec($sql);
    }

    public function save(Message $message): int
    {
        $sql = $message->id ? 'UPDATE chat_message SET' : 'INSERT INTO chat_message SET';
        $sql = sprintf('%s `from` = %d, `to` = %d, sent = \'%s\' ',$sql, $message->fromUserId, $message->toUserId, $message->sent);

        if ($message->content) {
            $sql = sprintf('%s, content = \'%s\'',$sql, $message->content);
        }

        if ($message->type) {
            $sql = sprintf('%s, type = \'%s\'',$sql, $message->type);
        }

        if ($message->id) {
            $sql = sprintf('%s WHERE id = %d',$sql, $message->id);
        }

        $this->connection->exec($sql);

        return $message->id == 0 ? (int) $this->connection->lastInsertId() : $message->id;
    }

    public function getById(int $id): Message
    {
        $sql = sprintf('SELECT id, `from`, `to`, sent, content, `type`, delivered, readed FROM chat_message WHERE id = %d', $id);
        $res = $this->connection->query($sql);
        $row = $res->fetch();

        return new Message(
            $row['id'],
            $row['from'],
            $row['to'],
            $row['sent'],
            $row['content'],
            $row['type'],
            $row['delivered'],
            $row['readed']
        );
    }
}