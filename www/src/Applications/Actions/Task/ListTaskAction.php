<?php
declare(strict_types=1);

namespace App\Application\Actions\Task;

use Psr\Http\Message\ResponseInterface as Response;

class ListTaskAction extends TaskAction
{
    protected function action(): Response
    {
        $pos = intval($this->request->getQueryParams()['start'] ?? 0);
        $count = intval($this->request->getQueryParams()['count'] ?? 0);
        $filters = $this->request->getQueryParams()['filter'] ?? [];

        return $this->respondWithData([
          "data" => $this->taskRepository->findAll($pos, $count, 0, $filters, 0, $this->user->getRulesId()),
          "pos" => $pos,
          "total_count" => $this->taskRepository->findAll($pos, $count, 0, $filters, 1, $this->user->getRulesId())
        ]);
    }
}
