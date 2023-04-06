<?php
declare(strict_types=1);

namespace App\Application\Actions\Vehicle;

use App\Domain\Vehicle\Request\CreateVehicleRequest;
use App\Domain\Vehicle\Request\UpdateVehicleRequest;
use App\Domain\Vehicle\Vehicle;
use Psr\Http\Message\ResponseInterface as Response;

class SaveVehicleAction extends VehicleAction
{
    protected function action(): Response
    {
      $from1c = $this->request->getQueryParams() && (int) $this->request->getQueryParams()['from1c'] ? (int) $this->request->getQueryParams()['from1c'] : 0;

      if ($from1c) {
        $vehicles = $this->request->getParsedBody();
        $vehiclesRespond = [];
        foreach ($vehicles as $vehicle) {
          try {
            $vehicle1c = $vehicle;
            $vehicle = $this->vehicleRepository->check1cVehicle($vehicle['ext_id']);
            if ($vehicle) {
              $vehicle->updateFromRequest(UpdateVehicleRequest::createFromArray($vehicle1c));
            } else {
              $vehicle = Vehicle::createFromRequest(CreateVehicleRequest::createFromArray($vehicle1c));
            }
            $vehiclesRespond[] = [$this->vehicleRepository->save($vehicle) => $vehicle->getExtId()];
          } catch (\Exception $e) {
            if (strpos($e->getMessage(), '1062')) $e = "Ошибка дублирования внешнего ID";
            $vehiclesRespond[] = [ 'res' => false, "message" => $e, "ext_id" => $vehicle['ext_id']];
          }
        }
          return $this->respondWithData($vehiclesRespond);
      } else {
        try {
          $id = (int)$this->request->getAttribute('id');

          if ($id) {
            $vehicle = $this->vehicleRepository->getById($id, $this->user->getRulesId());

            $vehicle->updateFromRequest(UpdateVehicleRequest::createFromArray($this->request->getParsedBody()));
          } else {
            $vehicle = Vehicle::createFromRequest(CreateVehicleRequest::createFromArray($this->request->getParsedBody()));
          }

          $vehicleId = $this->vehicleRepository->save($vehicle);
          return $this->respondWithData(['id' => $vehicleId]);
        } catch (\Exception $e) {
          if (strpos($e->getMessage(), '1062')) $e = "Ошибка дублирования внешнего ID";
          return $this->respondWithData([ 'res' => false, "message" => $e], 400 );
        }
      }
    }
}
