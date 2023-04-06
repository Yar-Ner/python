<?php
declare(strict_types=1);

namespace App\Application\Actions\User\Rule;

use App\Application\Actions\User\UserAction;
use App\Domain\Settings\Response\UserSettingsResponse;
use Psr\Http\Message\ResponseInterface as Response;

class ListUserRules extends UserAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        return $this->respondWithData(
            array_map(function (int $id) {
                return $this->ruleRepository->getHandleById($id);
            }, $this->user->getRulesId())
        );
    }
}
