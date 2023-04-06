<?php
declare(strict_types=1);

namespace App\Application\Actions\Vehicle;

use Psr\Http\Message\ResponseInterface as Response;

class ListVehicleAction extends VehicleAction
{
    protected function action(): Response
    {
        return $this->respondWithData($this->vehicleRepository->findAll($this->user->getRulesId()));
    }
}
