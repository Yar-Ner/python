<?php

declare(strict_types=1);

namespace App\Application\Actions\Task;

use App\Domain\Task\Request\PayloadRequest;
use Psr\Http\Message\ResponseInterface as Response;

class PayloadTaskAction extends TaskAction {

  protected function action(): Response
  {
    /** @var PayloadRequest $request */
    $request = $this->getDenormalizedRequest(PayloadRequest::class);

    if (!$request->orderId) return $this->respondWithData(['res' => false, 'message' => 'Отстуствует идентификатор заказа'], 400);

    try {
      $this->taskRepository->setPayload($request);
    } catch (\Error | \Exception $e) {
      return $this->respondWithData(['res' => false, 'message' => $e->getMessage()], 500);
    }

    return $this->respondWithData(['res' => true]);
  }

}