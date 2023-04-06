<?php
declare(strict_types=1);

namespace App\Application\Actions\Settings;

use App\Domain\Settings\DefaultSettings;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface as Response;

class SaveDefaultSettingsAction extends SettingsAction
{

    protected function action(): Response
    {
        $defaultSettings = json_decode($this->request->getParsedBody()['settings'], true);

        $response = [];
        foreach ($defaultSettings as $param) {
            $settingsParam = $this->defaultSettingsRepository->findOneByHandle($param['handle']);

            if ($settingsParam instanceof DefaultSettings) {
                $settingsParam->updateFromArray($param);
            } else {
                $settingsParam = DefaultSettings::createFromArray($param);
            }

            $this->defaultSettingsRepository->save($settingsParam, "default");
            $response[] = $settingsParam;
        }

        return $this->respondWithData($response);
    }
}
