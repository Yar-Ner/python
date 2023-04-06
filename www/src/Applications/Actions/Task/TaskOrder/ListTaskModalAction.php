<?php
declare(strict_types=1);

namespace App\Application\Actions\Task\TaskOrder;

use App\Application\Actions\Action;
use App\Domain\Task\TaskRepository;
use Psr\Http\Message\ResponseInterface as Response;

class ListTaskModalAction extends Action
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $taskId = (int) $this->resolveArg('id');

        return $this->respondWithData($this->get(TaskRepository::class)->findAll(0, 0, $taskId));
    }
}