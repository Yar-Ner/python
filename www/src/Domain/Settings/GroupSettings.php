<?php
declare(strict_types=1);

namespace App\Domain\Settings;


class GroupSettings extends DefaultSettings
{
    private $group_id;

    /**
     * @param int $group_id
     */
    public function setGroupId(int $group_id): void
    {
        $this->group_id = $group_id;
    }

    /**
     * @return int
     */
    public function getGroupId(): int
    {
        return $this->group_id;
    }

    public static function createFromArray(array $params): GroupSettings
    {
        return new self($params['handle'], $params['val']);
    }

    public function updateFromArray(array $params): void
    {
        $this->handle = $params['handle'] ?? $this->handle;
        if (isset($params['val'])) $this->setVal($params['val']);
        $this->setUpdated(new \DateTime());
    }
}
