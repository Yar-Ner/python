<?php
declare(strict_types=1);

namespace App\Application\Actions\Vehicle\Containers;

use Psr\Http\Message\ResponseInterface as Response;

class ListVehiclesContainersAction extends VehiclesContainersAction
{
  protected function action(): Response
  {
    return $this->respondWithData($this->vehiclesContainersRepository->findAll());
  }
}
