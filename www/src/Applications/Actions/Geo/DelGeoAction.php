<?php

declare(strict_types=1);

namespace App\Application\Actions\Geo;

use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface as Response;

class DelGeoAction extends GeoAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $id = (int)$this->request->getAttribute('id');

        $this->geoRepository->delete($id);

        return new \Slim\Psr7\Response(StatusCodeInterface::STATUS_OK);
    }
}