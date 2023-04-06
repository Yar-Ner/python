<?php
declare(strict_types=1);

namespace App\Application\Actions\User;

use App\Domain\User\Request\CreateUserRequest;
use App\Domain\User\Request\UpdateUserRequest;
use App\Domain\User\User;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface as Response;

class SaveUserAction extends UserAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $id = (int) $this->request->getAttribute('id');

        if ($id) {
            $user = $this->userRepository->getById($id);

            $user->updateFromRequest(UpdateUserRequest::createFromArray($this->request->getParsedBody()));
        } else {
            $user = User::createFromRequest(
                CreateUserRequest::createFromArray($this->request->getParsedBody())
            );
        }

        $response = $this->userRepository->save($user);

        if (isset($response['res']) && $response['res'] === false) return $this->respondWithData($response);
        return new \Slim\Psr7\Response(StatusCodeInterface::STATUS_OK);
    }
}
