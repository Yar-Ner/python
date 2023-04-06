<?php

declare(strict_types=1);


namespace App\Domain\SharedContext\BrowserDriver;


class Request
{
    public $method;
    public $url;
    public $headers = [];
    public $forcedCookies = [];
    public $data;

    private function __construct(
        string $method,
        string $url,
        array $data,
        array $forcedCookies = [],
        array $headers = []
    ) {
        $this->method = $method;
        $this->url = $url;
        $this->data = $data;
        $this->forcedCookies = $forcedCookies;
        $this->headers = $headers;
    }

    public static function make(
        string $method,
        string $url,
        array $data = [],
        array $forcedCookies = [],
        array $headers = []
    ): self {
        return new self ($method, $url, $data, $forcedCookies, $headers);
    }
}
