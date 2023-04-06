<?php


namespace App\Application\Actions\Task;

use App\Domain\Task\Request\WeightRequest;
use Psr\Http\Message\ResponseInterface as Response;

class WeightTaskAction extends TaskAction
{

  protected function action(): Response
  {
    $taskExtId = $this->resolveArg('id');
    $from1c = $this->request->getQueryParams()['from1c'] ?? 0;

    /** @var WeightRequest $request */
    $request = $this->getDenormalizedRequest(WeightRequest::class);

    if (!$request->emptyWeight && !$request->loadedWeight) {
      return $this->respondWithData(['res' => false, 'message' => "Ошибка, нужно указать хотя бы один из параметров веса!"], 400);
    }

    $this->taskRepository->setWeight($taskExtId, $request);

    if (!$from1c) {
      $arrayTo1c = [
        'id' => $taskExtId
      ];
      if ($request->emptyWeight) $arrayTo1c['empty_weight'] = $request->emptyWeight;
      if ($request->loadedWeight) $arrayTo1c['loaded_weight'] = $request->loadedWeight;
      $res = $this->dataSender->sendTaskWeight($arrayTo1c);
    }

    return $this->respondWithData(['id' => $taskExtId, 'webhook_res' => $res ?? null]);
  }
}