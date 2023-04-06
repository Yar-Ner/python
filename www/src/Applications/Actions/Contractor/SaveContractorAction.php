<?php
declare(strict_types=1);

namespace App\Application\Actions\Contractor;


use App\Domain\Contractor\Contractor;
use App\Domain\Contractor\Request\CreateContractorRequest;
use App\Domain\Contractor\Request\UpdateContractorRequest;
use Psr\Http\Message\ResponseInterface as Response;

class SaveContractorAction extends ContractorAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $from1c = $this->request->getQueryParams() && (int) $this->request->getQueryParams()['from1c'] ? (int) $this->request->getQueryParams()['from1c'] : 0;

        if ($from1c) {
          $contractors = $this->request->getParsedBody();
          $contractorsRespond = [];
          foreach ($contractors as $contractor) {
            try {
              $contractor1c = $contractor;
              $contractor = $this->contractorRepository->check1cContractor($contractor['ext_id']);
              if ($contractor) {
                $contractor->updateFromRequest(UpdateContractorRequest::createFromArray($contractor1c));
              } else {
                $contractor = Contractor::createFromRequest(CreateContractorRequest::createFromArray($contractor1c));
              }
              $contractorId = $this->contractorRepository->save($contractor);
              $contractorsRespond[] = [$contractorId => $contractor->getExtId()];
            } catch (\Exception $e) {
              if (strpos($e->getMessage(), '1062')) $e = "Ошибка дублирования внешнего ID";
              $vehiclesRespond[] = [ 'res' => false, "message" => $e, "ext_id" => $contractor['ext_id']];
            }
          }
          return $this->respondWithData($contractorsRespond);
        } else {
          try {
            $id = (int)$this->request->getAttribute('id');

            if ($id) {
              $contractor = $this->contractorRepository->getById($id);
              $contractor->updateFromRequest(UpdateContractorRequest::createFromArray($this->request->getParsedBody()));
            } else {
              $contractor = Contractor::createFromRequest(
                CreateContractorRequest::createFromArray($this->request->getParsedBody())
              );
            }

            $contractorId = $this->contractorRepository->save($contractor);
            return $this->respondWithData(['id' => $contractorId]);
          } catch (\Exception $e) {
            if (strpos($e->getMessage(), '1062')) $e = "Ошибка дублирования внешнего ID";
            return $this->respondWithData([ 'res' => false, "message" => $e], 400 );
          }
        }
    }
}