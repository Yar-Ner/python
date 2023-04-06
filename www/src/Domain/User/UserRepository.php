<?php
declare(strict_types=1);

namespace App\Domain\User;

interface UserRepository
{
    /**
     * @return User[]
     */
    public function findAll(): array;

    public function delete(int $id): void;

    /**
     * @throws UserNotFoundException
     */
    public function getById(int $id): User;

    public function save(User $user): ?array;

    public function findByToken(string $token): ?User;

    public function getIdByLoginAndPassword(string $login, string $password): int;

}
