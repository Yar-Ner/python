<?php
declare(strict_types=1);

namespace App\Application\Actions\User\Settings;

use App\Application\Actions\User\UserAction;
use App\Domain\Settings\Response\UserSettingsResponse;
use Psr\Http\Message\ResponseInterface as Response;

class ListUserSettings extends UserAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        return $this->respondWithData(
          new UserSettingsResponse(
            $this->defaultSettingsRepository->findAllUser($this->user->getId())
          )
        );
    }
}
