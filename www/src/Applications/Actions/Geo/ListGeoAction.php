<?php
declare(strict_types=1);

namespace App\Application\Actions\Geo;


use Psr\Http\Message\ResponseInterface as Response;

class ListGeoAction extends GeoAction
{

    protected function action(): Response
    {
        $geoIds = $this->request->getQueryParams()['geoIds'] ?? 0;
        $geoIds && $geoIds = explode (',', $geoIds);
        $areas = intval($this->request->getQueryParams()['areas'] ?? 0);

        $pos = intval($this->request->getQueryParams()['start'] ?? 0);
        $count = intval($this->request->getQueryParams()['count'] ?? 50);
        $filters = $this->request->getQueryParams()['filter'] ?? [];

        if (stripos($_SERVER['REQUEST_URI'], 'short')) {
          return $this->respondWithData($this->geoRepository->findAll($geoIds, $areas, $pos, 1000000, 0, $filters, 1));
        }

        return $this->respondWithData([
          "data" => $this->geoRepository->findAll($geoIds, $areas, $pos, $count, 0, $filters),
          "pos" => $pos,
          "total_count" => $this->geoRepository->findAll($geoIds, $areas, $pos, $count, 1, $filters)
        ]);
    }
}