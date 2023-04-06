<?php
declare(strict_types=1);

namespace App\Application\Actions\Monitoring;


use App\Domain\Monitoring\Monitoring;
use App\Domain\Monitoring\Request\CreateMonitoringRequest;
use Psr\Http\Message\ResponseInterface as Response;

class SaveMonitoringAction extends MonitoringAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $monitoring = Monitoring::createFromRequest(CreateMonitoringRequest::createFromArray($this->request->getParsedBody()));

        return $this->respondWithData([
          'id' => $this->monitoringRepository->save($monitoring)
        ]);
    }
}