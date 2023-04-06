<?php
declare(strict_types=1);

namespace App\Application\Actions\Settings;

use Psr\Http\Message\ResponseInterface as Response;

class ListSettingsUserAction extends SettingsAction
{

    protected function action(): Response
    {
        $id = (int) $this->resolveArg('id');

        return $this->respondWithData($this->defaultSettingsRepository->findAllUser($id));
    }
}
