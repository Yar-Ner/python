<?php
declare(strict_types=1);

namespace App\Application\Actions\Vehicle\Types;

use Psr\Http\Message\ResponseInterface as Response;

class ViewVehiclesTypesAction extends VehiclesTypesAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $vehicleType = (int) $this->resolveArg('id');

        return $this->respondWithData($this->vehiclesTypesRepository->getById($vehicleType));
    }
}
