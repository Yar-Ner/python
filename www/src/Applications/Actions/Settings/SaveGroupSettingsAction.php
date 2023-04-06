<?php
declare(strict_types=1);

namespace App\Application\Actions\Settings;

use App\Domain\Settings\DefaultSettings;
use App\Domain\Settings\GroupSettings;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface as Response;

class SaveGroupSettingsAction extends SettingsAction
{

    protected function action(): Response
    {
        $groupSettings = json_decode($this->request->getParsedBody()['settings'], true);
        $id = (int)$this->request->getAttribute('id');

        $response = [];
        foreach ($groupSettings as $param) {
            $settingsParam = $this->defaultSettingsRepository->getGroupSettingByHandle($param['handle'], $id);

            if ($settingsParam instanceof GroupSettings) {
                $settingsParam->updateFromArray($param);
            } else {
                $settingsParam = GroupSettings::createFromArray($param);
            }

            $settingsParam->setGroupId($id);

            $this->defaultSettingsRepository->save($settingsParam, 'group');
            $response[] = $settingsParam;
        }

        return $this->respondWithData($response);
    }
}
