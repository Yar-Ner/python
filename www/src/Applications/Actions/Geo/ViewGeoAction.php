<?php

declare(strict_types=1);

namespace App\Application\Actions\Geo;

use Psr\Http\Message\ResponseInterface as Response;

class ViewGeoAction extends GeoAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $id = (int)$this->request->getAttribute('id');

        $geo = $this->geoRepository->getById($id);

        return $this->respondWithData($geo);
    }
}