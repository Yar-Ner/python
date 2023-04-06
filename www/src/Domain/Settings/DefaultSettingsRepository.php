<?php
declare(strict_types=1);

namespace App\Domain\Settings;


interface DefaultSettingsRepository
{
    /**
     * @return DefaultSettings[]
     */
    public function findAll(): array;

    public function findAllUser(int $id): array;

    public function findAllGroup(int $id): array;

    public function findOneByHandle(string $handle): ?DefaultSettings;

    public function save(DefaultSettings $settings, string $type): void;

    public function defaultUserHandle(int $id, string $handle): void;

    public function defaultGroupHandle(int $id, string $handle): void;

}
