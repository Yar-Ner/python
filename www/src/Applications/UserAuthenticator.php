<?php


namespace App\Application;


use App\Domain\User\User;
use App\Domain\User\UserRepository;
use App\Domain\User\UserToken;
use App\Domain\User\UserTokenRepository;

class UserAuthenticator
{
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var UserTokenRepository
     */
    private $userTokenRepository;

    public function __construct(UserRepository $userRepository, UserTokenRepository $userTokenRepository)
    {
        $this->userRepository = $userRepository;
        $this->userTokenRepository = $userTokenRepository;
    }

    public function auth(string $login, string $password, ?string $ip): array
    {
        $userId = $this->userRepository->getIdByLoginAndPassword($login, $password);
        $user = $this->userRepository->getById($userId);
        $token = UserToken::generateByUserIdAndIp($userId, $ip);
        $this->userTokenRepository->save($token);

        $userInfo['token'] = $token->getToken();
        $userInfo["id"] = $userId;
        $userInfo["fullname"] = $user->getFullname();

        return $userInfo;
    }
}
