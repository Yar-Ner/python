<?php


namespace App\Application\Providers;


use App\Domain\User\User;
use App\Domain\User\UserRepository;
use App\Domain\User\UserToken;
use Psr\Http\Message\ServerRequestInterface;

class UserProvider
{

    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct( UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function getUser(ServerRequestInterface $request): ?User
    {
        $token = $request->getHeader('token');

        if (is_array($token) && $token) {
            $token = is_string(current($token)) ? current($token) : null;
        } elseif (array_key_exists('token', $request->getCookieParams())) {
            $token = str_replace('"', '', $request->getCookieParams()['token']);
        }

        if ($token) {
            return $this->userRepository->findByToken($token);
        }

        return null;
    }
}
