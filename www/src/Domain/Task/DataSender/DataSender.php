<?php

declare(strict_types=1);


namespace App\Domain\Task\DataSender;


use App\Domain\Distance\TaskDistance;

interface DataSender
{

    public function sendTaskDistance(TaskDistance $taskDistance);

}
