<?php
declare(strict_types=1);

namespace App\Application\Actions\Vehicle\Types;

use Psr\Http\Message\ResponseInterface as Response;

class DelVehiclesTypesAction extends VehiclesTypesAction
{

    protected function action(): Response
    {
        $id = (int) $this->request->getAttribute('id');
        $id = $this->vehiclesTypesRepository->delete($id);

        return $this->respondWithData(['id' => $id]);
    }
}