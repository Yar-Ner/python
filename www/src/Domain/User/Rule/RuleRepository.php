<?php
declare(strict_types=1);

namespace App\Domain\User\Rule;


interface RuleRepository
{
    /**
     * @return Rule[]
     */
    public function findAll(): array;

    public function getById(int $id): Rule;

    public function getHandleById(int $id): string;

    public function save(Rule $rule): void;

    public function delete(int $id): string;

    public function deleteById(int $id): void;

    public function getHandleByIds(array $ids): array;

}
