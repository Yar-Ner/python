<?php
declare(strict_types=1);

namespace App\Application\Actions\Vehicle;

use App\Application\Actions\Action;
use App\Domain\Vehicle\VehicleRepository;
use Psr\Http\Message\ResponseInterface as Response;

class ViewVehicleAction extends Action
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $userId = (int) $this->resolveArg('id');

        /** @var VehicleRepository $repo */
        $repo = $this->get(VehicleRepository::class);

        return $this->respondWithData($repo->getById($userId, $this->user->getRulesId()));
    }
}
