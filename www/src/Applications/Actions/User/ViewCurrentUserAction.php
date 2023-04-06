<?php
declare(strict_types=1);

namespace App\Application\Actions\User;

use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface as Response;

class ViewCurrentUserAction extends UserAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        return $this->respondWithData(
            array_merge(
                $this->user->jsonSerialize(),
                [
                    'rules' =>
                        array_unique(
                            array_merge(
                                $this->ruleRepository->getHandleByIds($this->user->getRulesId()),
                                ['user']
                            )
                        )
                ]
            )
        );
    }
}
