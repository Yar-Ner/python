<?php
declare(strict_types=1);

namespace App\Domain\Device;

use App\Domain\User\User;

interface DeviceRepository
{
    /**
     * @return Device[]
     */
    public function findAll(): array;

    public function delete(int $id): void;

    /**
     * @throws DeviceNotFoundException
     */
    public function getById(int $id): Device;

    public function save(Device $device): void;

    /**
     * @return Device[]
     */
    public function findByUser(User $user): array;

}
