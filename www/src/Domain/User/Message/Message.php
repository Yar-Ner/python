<?php

declare(strict_types=1);

namespace App\Domain\User\Message;


class Message
{
    public $fromUserId;

    public $toUserId;

    public $sent;

    public $content;

    public $type;

    public $delivered;

    public $readed;

    public $id;

    public function __construct(
        ?int $id,
        int $fromUserId,
        int $toUserId,
        ?string $sent,
        ?string $content,
        ?int $type,
        ?string $delivered,
        ?string $readed
    ) {
        $this->id = $id;
        $this->fromUserId = $fromUserId;
        $this->toUserId = $toUserId;
        $this->sent = $sent;
        $this->content = $content;
        $this->type = $type;
        $this->delivered = $delivered;
        $this->readed = $readed;
    }


}