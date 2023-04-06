<?php

declare(strict_types=1);


namespace App\Domain\Settings\Response;


class UserSettingsResponse implements \JsonSerializable
{
    /**
     * @var array
     */
    public $settings;

    public function __construct(array $settings)
    {
        $this->settings = $settings;
    }

    public function jsonSerialize(): array
    {
      $arrMerged = [];
      foreach ($this->settings as $setting) {
        $arrMerged[] = [
          'handle' => $setting['handle'],
          'val' => $setting['val'],
          'main' => $setting['main'],
        ];
      }
      return $arrMerged;
    }
}