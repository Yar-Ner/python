<?php


namespace App\Domain\User;


class UserToken implements \JsonSerializable
{
    private $id;
    private $userId;
    private $token;
    private $hwid;
    private $pcode;
    private $version;
    private $ip;
    private $issued;
    private $updated;
    private $expire;

    public function __construct(?int $id, $userId, $token, $hwid, $pcode, $version, $ip, $issued, $updated, $expire)
    {
        $this->id = $id;
        $this->userId = $userId;
        $this->token = $token;
        $this->hwid = $hwid;
        $this->pcode = $pcode;
        $this->version = $version;
        $this->ip = $ip;
        $this->issued = $issued;
        $this->updated = $updated;
        $this->expire = $expire;
    }

    public static function generateByUserIdAndIp(int $userId, ?string $ip): UserToken
    {
        return new UserToken(
            null,
            $userId,
            substr(sha1(sprintf("%s%d", $userId, round(microtime(1) * 1000))), 0, 32),
            '',
            'web',
            '1.0',
            $ip,
            new \DateTime(),
            new \DateTime(),
            (new \DateTime())->add(new \DateInterval('P1D'))
        );
    }

    public function getToken()
    {
        return $this->token;
    }

    public function getHwid()
    {
        return $this->hwid;
    }

    public function getPcode()
    {
        return $this->pcode;
    }

    public function getVersion()
    {
        return $this->version;
    }

    public function getIp()
    {
        return $this->ip;
    }

    public function getIssued()
    {
        return $this->issued;
    }

    public function getUpdated()
    {
        return $this->updated;
    }


    public function getExpire()
    {
        return $this->expire;
    }

    public function getUserId()
    {
        return $this->userId;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function jsonSerialize(): array
    {
        return ['token' => $this->token];
    }
}