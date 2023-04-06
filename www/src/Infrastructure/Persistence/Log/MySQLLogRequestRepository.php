<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Log;

use App\Domain\SharedContext\Log\LogRequest;
use App\Domain\SharedContext\Log\LogRequestRepository;

class MySQLLogRequestRepository implements LogRequestRepository
{
    /**
     * @var \PDO[]
     */
    private $connection;

    public function __construct(\PDO $connection)
    {
        $this->connection = $connection;
    }

    public function log (LogRequest $logRequest): int
    {
        $sql = sprintf("
            INSERT INTO log_requests 
            SET `datetime` = '%s', `user_id` = '%d', 
            `url` = '%s', `response` = '%s'",
            $logRequest->getDatetime(),
            $logRequest->getUserId(),
            $logRequest->getUrl(),
            addcslashes($logRequest->getResponse(), '\'')
        );
        if ($logRequest->getRequest()) {
            $sql .= ", `request` = ".json_encode($logRequest->getRequest());
        }

        $this->connection->exec($sql);

        return (int)$this->connection->lastInsertId();
    }
}
