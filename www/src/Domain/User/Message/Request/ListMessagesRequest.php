<?php

declare(strict_types=1);

namespace App\Domain\User\Message\Request;

class ListMessagesRequest
{
    public $userId;

    /**
     * @var string
     */
    public $from;

    /**
     * @var string
     */
    public $to;

}
