<?php


namespace App\Domain\User;


interface UserTokenRepository
{

    public function save(UserToken $token): UserToken;

    public function delete(UserToken $token): void;

    public function deleteByStringAndUserId(string $token, int $userId): void;

}