<?php
declare(strict_types=1);

namespace App\Application\Actions\Settings;

use App\Domain\Settings\UserSettings;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface as Response;

class SaveUserSettingsAction extends SettingsAction
{

    protected function action(): Response
    {
        $userSettings = json_decode($this->request->getParsedBody()['settings'], true);
        $id = (int)$this->request->getAttribute('id');

        $response = [];
        foreach ($userSettings as $param) {
            $settingsParam = $this->defaultSettingsRepository->getUserSettingByHandle($param['handle'], $id);

            if ($settingsParam instanceof UserSettings) {
                $settingsParam->updateFromArray($param);
            } else {
                $settingsParam = UserSettings::createFromArray($param);
            }

            $settingsParam->setUserId($id);

            $this->defaultSettingsRepository->save($settingsParam, 'user');
            $response[] = $settingsParam;
        }

        return $this->respondWithData($response);
    }
}
