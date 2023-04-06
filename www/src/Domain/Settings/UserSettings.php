<?php
declare(strict_types=1);

namespace App\Domain\Settings;


class UserSettings extends DefaultSettings
{
    private $user_id;

    /**
     * @param int $user_id
     */
    public function setUserId($user_id): void
    {
        $this->user_id = $user_id;
    }

    /**
     * @return int
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    public static function createFromArray(array $params): UserSettings
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
