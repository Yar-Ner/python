<?php

declare(strict_types=1);

namespace App\Domain\Photo\Response;


use App\Domain\Photo\Photo;

class ListPhotoResponse implements \JsonSerializable
{
    private $photos;

    public function __construct(array $photos)
    {
        $this->photos = $photos;
    }

    public function jsonSerialize(): array
    {
      $arrMerged = [];
      foreach ($this->photos as $photo) {
        $arrMerged[] = [
          'id' => $photo->getId(),
          'acl_user_id' => $photo->getUserId(),
          'uploaded' => $photo->getUserId(),
          'url' => $photo->getPath()
        ];
      }
      return $arrMerged;
    }
}