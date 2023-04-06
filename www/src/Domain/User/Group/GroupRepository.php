<?php
declare(strict_types=1);

namespace App\Domain\User\Group;


interface GroupRepository
{
    /**
     * @return Group[]
     */
    public function findAll(): array;

    public function getById(int $id): Group;

    public function save(Group $rule): void;

    public function delete(int $id): string;

    public function deleteById(int $id): void;

}
