<?php
declare(strict_types=1);

namespace App\Application\Actions\User\Rule;


use App\Application\Actions\ActionPayload;
use App\Domain\User\Rule\Request\CreateGroupRequest;
use App\Domain\User\Rule\Rule;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface as Response;

class ViewRulesAction extends RuleAction
{

    protected function action(): Response
    {
        $id = (int) $this->request->getAttribute('id');

        $rule = $this->ruleRepository->getById($id);

        return $this->respondWithData($rule);
    }
}