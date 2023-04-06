<?php

declare(strict_types=1);

namespace App\Domain\User\Message\Response;


class ListMessagesResponse implements \JsonSerializable
{
    /**
     * @var array
     */
    private $messages;

    public function __construct(array $messages)
    {
        $this->messages = $messages;
    }

    public function jsonSerialize(): array
    {
        $arrMerged = [];
        foreach ($this->messages as $message) {
          $arrMerged[] = [
            'id' => $message->id,
            'from' => $message->fromUserId,
            'to' => $message->toUserId,
            'type' => $message->type,
            'sent' => $message->sent,
            'readed' => $message->readed,
            'delivered' => $message->delivered,
            'content' => $message->content,
          ];
        }
        return $arrMerged;
    }
}