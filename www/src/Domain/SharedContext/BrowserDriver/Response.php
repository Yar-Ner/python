<?php

declare(strict_types=1);


namespace App\Domain\SharedContext\BrowserDriver;

class Response
{
    public $headers;
    public $cookies;
    public $content;
    public $url;
    public $status;

    private function __construct(int $status, string $url, string $content, array $cookies, array $headers)
    {
        $this->status = $status;
        $this->url = $url;
        $this->content = $content;
        $this->cookies = $cookies;
        $this->headers = $headers;
    }

    public static function make(
        int $status,
        string $url,
        string $content,
        array $cookies = [],
        array $headers = []
    ): Response {
        return new self($status, $url, $content, $cookies, $headers);
    }
}
