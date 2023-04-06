<?php
declare(strict_types=1);

namespace App\Application\Actions\Settings;

use Psr\Http\Message\ResponseInterface as Response;

class DefaultGroupHandleAction extends SettingsAction
{

    protected function action(): Response
    {
        $id = (int)$this->request->getAttribute('id');
        $handle = $this->request->getAttribute('handle');

        $this->defaultSettingsRepository->defaultGroupHandle($id, $handle);

        return $this->respondWithData(['result' => true]);
    }
}
