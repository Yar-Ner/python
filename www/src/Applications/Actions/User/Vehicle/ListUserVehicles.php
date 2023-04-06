<?php
declare(strict_types=1);

namespace App\Application\Actions\User\Vehicle;

use App\Application\Actions\User\UserAction;
use App\Domain\Vehicle\Response\UserVehiclesResponse;
use App\Domain\Vehicle\VehicleRepository;
use Psr\Http\Message\ResponseInterface as Response;

class ListUserVehicles extends UserAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        /* @var VehicleRepository $vehicleRepository */
        $vehicleRepository = $this->get(VehicleRepository::class);
        return $this->respondWithData(
            new UserVehiclesResponse(
              $vehicleRepository->findAllByUserId(
                    $this->user->getId(),
                    $this->user->getRulesId()
                )
            )
        );
    }
}
