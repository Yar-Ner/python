<?php

declare(strict_types=1);

namespace App\Domain\SharedContext;


class File
{
    private $filePath;

    private $realName;

    public function __construct(string $filePath, string $realName)
    {
        $this->filePath = $filePath;
        $this->realName = $realName;
    }

    public function getFilePath(): string
    {
        return $this->filePath;
    }

    public function getRealName(): string
    {
        return $this->realName;
    }

}
