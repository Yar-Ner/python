<?php
declare(strict_types=1);

namespace App\Domain\Settings;


use DateTime;

class DefaultSettings implements \JsonSerializable
{
    private $handle;

    private $val;

    private $updated;

    public function __construct(string $handle,string $val, ?\DateTime $updated = null)
    {
        $this->handle = $handle;
        $this->val = $val;
        if ($updated) $this->updated = $updated->format('Y-m-d H:i:s');
    }

    public function getHandle(): string
    {
        return $this->handle;
    }

    public function getVal(): string
    {
        return $this->val;
    }

    public function setVal(string $val): void
    {
        $this->val = $val;
    }

    public function getUpdated(): ?string
    {
        return $this->updated;
    }

    public function setUpdated(DateTime $updated): void
    {
        $this->updated = $updated;
    }

    public function jsonSerialize()
    {
        return [
            'val' => $this->val,
            'handle' => $this->handle
        ];
    }

    public static function createFromArray(array $params): DefaultSettings
    {
        return new self($params['handle'], $params['val']);
    }

    public function updateFromArray(array $params): void
    {
        $this->handle = $params['handle'] ?? $this->handle;
        $this->val = $params['val'] ?? $this->val;
        $this->updated = new \DateTime();
    }

}