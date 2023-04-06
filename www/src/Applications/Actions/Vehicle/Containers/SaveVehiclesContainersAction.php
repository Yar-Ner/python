<?php
declare(strict_types=1);

namespace App\Application\Actions\Vehicle\Containers;

use App\Domain\SharedContext\Log\LogRequest;
use App\Domain\SharedContext\Log\LogRequestRepository;
use App\Domain\Vehicle\Containers\Request\CreateVehiclesContainersRequest;
use App\Domain\Vehicle\Containers\Request\UpdateVehiclesContainersRequest;
use App\Domain\Vehicle\Containers\VehiclesContainers;
use Psr\Http\Message\ResponseInterface as Response;

class SaveVehiclesContainersAction extends VehiclesContainersAction
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
      foreach ($body as $container) {
        try {
          $vehicleContainer = $this->vehiclesContainersRepository->getByExtId($container['ext_id']);

          if ($vehicleContainer) {
            $vehicleContainer->updateFromRequest(UpdateVehiclesContainersRequest::createFromArray($container));
          } else {
            $vehicleContainer = VehiclesContainers::createFromRequest(CreateVehiclesContainersRequest::createFromArray($container));
          }
          $vehicleContainerId = $this->vehiclesContainersRepository->save($vehicleContainer);
          $ids[] = $vehicleContainerId;
        } catch (\Exception | \Error $e) {
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
          $vehicleContainer = $this->vehiclesContainersRepository->getById($id);

          $vehicleContainer->updateFromRequest(UpdateVehiclesContainersRequest::createFromArray($this->request->getParsedBody()));
        } else {
          $vehicleContainer = VehiclesContainers::createFromRequest(CreateVehiclesContainersRequest::createFromArray($this->request->getParsedBody()));
        }

        $vehicleContainerId = $this->vehiclesContainersRepository->save($vehicleContainer);
      } catch (\Exception | \Error $e) {
        $logRequest = LogRequest::createFromArray(
          date("Y-m-d H:i:s"),
          $this->user->getId(),
          $_SERVER['REQUEST_URI'],
          json_encode($body),
          $e->getMessage()
        );
        $logRequestId = $logRequestRepository->log($logRequest);
      }
      return $this->respondWithData(['id' => $vehicleContainerId ?? null, 'logRequestId' => $logRequestId ?? null]);
    }
  }
}
