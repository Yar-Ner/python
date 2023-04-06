<?php

declare(strict_types=1);

namespace App\Domain\Monitoring\Request;


class ViewMonitoringRequest
{
  public $tasksId;

  public $from;

  public $to;

  public $limit;
}
