<?php

declare(strict_types=1);

namespace App\Application\Actions\Geo;


use App\Domain\Geo\Geo;
use App\Domain\Geo\Request\CreateGeoRequest;
use App\Domain\Geo\Request\UpdateGeoRequest;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface as Response;

class SaveGeoAction extends GeoAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $from1c = $this->request->getQueryParams() && (int) $this->request->getQueryParams()['from1c'] ? (int) $this->request->getQueryParams()['from1c'] : 0;

      if ($from1c) {
        $geoobjects = $this->request->getParsedBody();
        $geoobjectsRespond = [];
        foreach ($geoobjects as $geoobject) {
          try {
            $geoobject1c = $geoobject;
            $geoobject = $this->geoRepository->check1cGeoobject($geoobject['ext_id']);
            if ($geoobject) {
              $geoobject->updateFromRequest(UpdateGeoRequest::createFromArray($geoobject1c));
            } else {
              $geoobject = Geo::createFromRequest(CreateGeoRequest::createFromArray($geoobject1c));
            }
            $geoId = $this->geoRepository->save($geoobject);
            $geoobjectsRespond[] = [$geoId => $geoobject->getExtId()];
          } catch (\Exception $e) {
            if (strpos($e->getMessage(), '1062')) $e = "Ошибка дублирования внешнего ID";
            $geoobjectsRespond[] = [ 'res' => false, "message" => $e, "ext_id" => $geoobject->getExtId()];
          }
        }
        return $this->respondWithData($geoobjectsRespond);
      } else {
        try {
          $id = (int)$this->request->getAttribute('id');

          if ($id) {
            $geo = $this->geoRepository->getById($id);

            $geo->updateFromRequest(UpdateGeoRequest::createFromArray($this->request->getParsedBody()));
          } else {
            $geo = Geo::createFromRequest(
              CreateGeoRequest::createFromArray($this->request->getParsedBody())
            );
          }

          $geoId = $this->geoRepository->save($geo);
          return $this->respondWithData(['id' => $geoId]);
        } catch (\Exception $e) {
          if (strpos($e->getMessage(), '1062')) $e = "Ошибка дублирования внешнего ID";
          return $this->respondWithData([ 'res' => false, "message" => $e], 400 );
        }
      }
    }
}