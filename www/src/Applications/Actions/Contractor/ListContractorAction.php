<?php
declare(strict_types=1);

namespace App\Application\Actions\Contractor;


use Psr\Http\Message\ResponseInterface as Response;

class ListContractorAction extends ContractorAction
{

    protected function action(): Response
    {
        $pos = intval($this->request->getQueryParams()['start'] ?? 0);
        $count = intval($this->request->getQueryParams()['count'] ?? 50);
        $filters = $this->request->getQueryParams()['filter'] ?? [];

        return $this->respondWithData([
            "data" => $this->contractorRepository->findAll($pos, $count, 0, $filters),
            "pos" => $pos,
            "total_count" => $this->contractorRepository->findAll($pos, $count, 1, $filters)
        ]);
    }
}