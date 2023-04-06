<?php

declare(strict_types=1);

namespace App\Domain\User\Message\Request;

class MarkAsDeliveredRequest
{
    /**
     * @var int[]
     */
    public $ids;

    public function setId (string $ids) {
      $this->id = explode(',', $ids);
    }

}
