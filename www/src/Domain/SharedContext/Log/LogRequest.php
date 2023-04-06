<?php

namespace App\Domain\SharedContext\Log;

class LogRequest
{
    private $datetime;

    private $userId;

    private $url;

    private $request;

    private $response;

    public function __construct(string $datetime, int $userId, string $url, ?string $request = null, string $response)
    {
        $this->datetime = $datetime;
        $this->userId = $userId;
        $this->url = $url;
        $this->request = $request;
        $this->response = $response;
    }

    public static function createFromArray(string $datetime, int $userId, string $url, ?string $request = null, string $response): LogRequest
    {
        return new LogRequest($datetime, $userId, $url, $request, $response);
    }

    public function getDatetime(): string
    {
        return $this->datetime;
    }

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getRequest(): ?string
    {
        return $this->request;
    }

    public function getResponse(): string
    {
        return $this->response;
    }
}