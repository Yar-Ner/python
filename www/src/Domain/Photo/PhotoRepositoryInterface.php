<?php

declare(strict_types=1);

namespace App\Domain\Photo;


interface PhotoRepositoryInterface
{
    public function save(Photo $photo): int;

    public function find($request): array;
}
