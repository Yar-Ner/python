<?php
declare(strict_types=1);

namespace App\Application\Actions\Vehicle\Containers;

use Psr\Http\Message\ResponseInterface as Response;

class ViewVehiclesContainersAction extends VehiclesContainersAction
{
  /**
   * {@inheritdoc}
   */
  protected function action(): Response
  {
    $vehicleContainer = (int) $this->resolveArg('id');

    return $this->respondWithData($this->vehiclesContainersRepository->getById($vehicleContainer));
  }
}
