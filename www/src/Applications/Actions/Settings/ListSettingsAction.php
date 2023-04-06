<?php
declare(strict_types=1);

namespace App\Application\Actions\Settings;

use Psr\Http\Message\ResponseInterface as Response;

class ListSettingsAction extends SettingsAction
{

    protected function action(): Response
    {
        return $this->respondWithData($this->defaultSettingsRepository->findAll());
    }
}
