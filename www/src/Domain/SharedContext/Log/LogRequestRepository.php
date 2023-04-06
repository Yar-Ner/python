<?php

declare(strict_types=1);


namespace App\Domain\SharedContext\Log;


interface LogRequestRepository
{
    public function log(LogRequest $logRequest): int;
}
