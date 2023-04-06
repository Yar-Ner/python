<?php
declare(strict_types=1);

namespace App\Application\Actions\Vehicle\Containers;

use Psr\Http\Message\ResponseInterface as Response;

class DelVehiclesContainersAction extends VehiclesContainersAction
{

  protected function action(): Response
  {
    $id = (int) $this->request->getAttribute('id');
    $id = $this->vehiclesContainersRepository->delete($id);

    return $this->respondWithData(['id' => $id]);
  }
}