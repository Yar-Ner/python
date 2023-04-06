<?php
declare(strict_types=1);

namespace App\Application\Actions\Vehicle\Types;

use App\Domain\SharedContext\Log\LogRequest;
use App\Domain\SharedContext\Log\LogRequestRepository;
use App\Domain\Vehicle\Types\Request\CreateVehiclesTypesRequest;
use App\Domain\Vehicle\Types\Request\UpdateVehiclesTypesRequest;
use App\Domain\Vehicle\Types\VehiclesTypes;
use PhpParser\Error;
use Psr\Http\Message\ResponseInterface as Response;

class SaveVehiclesTypesAction extends VehiclesTypesAction
{
    protected function action(): Response
    {
      /** @var LogRequestRepository $logRequestRepository */
      $logRequestRepository = $this->get(LogRequestRepository::class);

      $id = (int)$this->request->getAttribute('id');
      $body = $this->request->getParsedBody();
      $from1c = $this->request->getQueryParams()['from1c'] ?? 0;

      if ($from1c) {
        $ids = [];
        $logRequestIds = [];
        foreach ($body as $type) {
          try {
            $vehicleType = $this->vehiclesTypesRepository->getByExtId($type['ext_id']);
            if ($vehicleType) {
              $vehicleType->updateFromRequest(UpdateVehiclesTypesRequest::createFromArray($type));
            } else {
              $vehicleType = VehiclesTypes::createFromRequest(CreateVehiclesTypesRequest::createFromArray($type));
            }

            $vehicleTypeId = $this->vehiclesTypesRepository->save($vehicleType);
            $ids[] = $vehicleTypeId;
          } catch (\Error | \Exception $e) {
            $logRequest = LogRequest::createFromArray(
              date("Y-m-d H:i:s"),
              $this->user->getId(),
              $_SERVER['REQUEST_URI'],
              json_encode($body),
              $e->getMessage()
            );
            $logRequestIds[] = $logRequestRepository->log($logRequest);
          }
        }
        return $this->respondWithData(['ids' => $ids ?? null, 'logRequestIds' => $logRequestIds ?? null]);
      } else {
        try {
          if ($id) {
            $vehicleType = $this->vehiclesTypesRepository->getById($id);

            $vehicleType->updateFromRequest(UpdateVehiclesTypesRequest::createFromArray($body));
          } else {
            $vehicleType = VehiclesTypes::createFromRequest(CreateVehiclesTypesRequest::createFromArray($body));
          }

          $vehicleTypeId = $this->vehiclesTypesRepository->save($vehicleType);
        } catch (\Error | \Exception $e) {
          $logRequest = LogRequest::createFromArray(
            date("Y-m-d H:i:s"),
            $this->user->getId(),
            $_SERVER['REQUEST_URI'],
            json_encode($body),
            $e->getMessage()
          );
          $logRequestId = $logRequestRepository->log($logRequest);
        }
        return $this->respondWithData(['id' => $vehicleTypeId ?? null, 'logRequestId' => $logRequestId ?? null]);
      }
    }
}
