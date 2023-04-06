<?php
declare(strict_types=1);

namespace App\Application\Actions\User\Rule;


use Psr\Http\Message\ResponseInterface as Response;

class ListRulesAction extends RuleAction
{
    protected function action(): Response
    {
        return $this->respondWithData($this->ruleRepository->findAll());
    }
}