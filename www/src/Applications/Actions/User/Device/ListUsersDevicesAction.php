<?php
declare(strict_types=1);

namespace App\Application\Actions\User\Device;

use App\Application\Actions\User\UserAction;
use App\Domain\Device\Response\UserDevicesResponse;
use Psr\Http\Message\ResponseInterface as Response;

class ListUsersDevicesAction extends UserAction
{
    protected function action(): Response
    {
        return $this->respondWithData(
            new UserDevicesResponse(
                $this->deviceRepository->findByUser($this->user)
            )
        );
    }
}
