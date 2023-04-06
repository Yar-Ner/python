<?php


namespace App\Application\Actions\Photo;


use App\Application\Actions\Action;
use App\Domain\Photo\PhotoRepositoryInterface;
use App\Domain\Photo\Request\ListPhotoRequest;
use App\Domain\Photo\Response\ListPhotoResponse;
use Psr\Http\Message\ResponseInterface as Response;

class ListPhotoAction extends PhotoAction
{

    protected function action(): Response
    {
        $request['orders_id'] = $this->request->getQueryParams()['orders_id'] ?? null;
        $request['vehicles_id'] = $this->request->getQueryParams()['vehicles_id'] ?? null;
        $request['alarms_id'] = $this->request->getQueryParams()['alarms_id'] ?? null;
        if (!isset($request['orders_id'])) {
            return $this->respondWithData(['result' => false, 'massage' => 'Отсутствует ID задания']);
        }
        return $this->respondWithData(
            new ListPhotoResponse(
                $this->photoRepositoryInterface->find($request)
            )
        );
    }
}
