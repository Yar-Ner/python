<?php
declare(strict_types=1);

namespace App\Application\Actions\Vehicle;

use App\Application\Actions\Action;
use App\Domain\Vehicle\VehicleRepository;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface as Response;

class DelVehicleAction extends Action
{

    protected function action(): Response
    {
        $id = (int) $this->request->getAttribute('id');

        /** @var VehicleRepository $repo */
        $repo = $this->get(VehicleRepository::class);

        $repo->delete($id);

        return new \Slim\Psr7\Response(StatusCodeInterface::STATUS_OK);
    }
}