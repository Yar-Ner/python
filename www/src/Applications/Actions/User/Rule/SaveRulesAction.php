<?php
declare(strict_types=1);

namespace App\Application\Actions\User\Rule;


use App\Domain\User\Rule\Request\CreateRuleRequest;
use App\Domain\User\Rule\Request\UpdateRuleRequest;
use App\Domain\User\Rule\Rule;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface as Response;

class SaveRulesAction extends RuleAction
{

    protected function action(): Response
    {
        $id = (int) $this->request->getAttribute('id');

        if ($id) {
            $rule = $this->ruleRepository->getById($id);

            $rule->updateFromRequest(UpdateRuleRequest::createFromArray($this->request->getParsedBody()));
        } else {
            $rule = Rule::createFromRequest(
                CreateRuleRequest::createFromArray($this->request->getParsedBody())
            );
        }

        $this->ruleRepository->save($rule);

        return new \Slim\Psr7\Response(StatusCodeInterface::STATUS_OK);
    }
}