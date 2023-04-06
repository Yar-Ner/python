<?php

declare(strict_types=1);

namespace App\Domain\User\Message\Request;

class SendRequest
{

    /**
     * @var string
     */
    public $content;

    public $to;

    public $id;

}
