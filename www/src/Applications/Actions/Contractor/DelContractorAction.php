<?php
declare(strict_types=1);

namespace App\Application\Actions\Contractor;

use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface as Response;

class DelContractorAction extends ContractorAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $id = (int) $this->request->getAttribute('id');

        $this->contractorRepository->delete($id);

        return new \Slim\Psr7\Response(StatusCodeInterface::STATUS_OK);
    }
}