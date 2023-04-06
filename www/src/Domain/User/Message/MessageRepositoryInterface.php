<?php


namespace App\Domain\User\Message;


interface MessageRepositoryInterface
{

    public function getById(int $id): Message;

    /**
     * @return Message[]
     */
    public function getMessages(int $toUserId, string $fromDate, string $toDate): array;

    public function markAsDelivered(array $ids): void;

    public function markAsRead(array $ids): void;

    public function save(Message $message): int;

}