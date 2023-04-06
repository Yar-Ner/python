<?php
declare(strict_types=1);

namespace App\Application\Actions\Contractor;

use Psr\Http\Message\ResponseInterface as Response;

class ViewContractorAction extends ContractorAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $id = (int) $this->request->getAttribute('id');

        $contractor = $this->contractorRepository->getById($id);

        return $this->respondWithData($contractor);
    }
}