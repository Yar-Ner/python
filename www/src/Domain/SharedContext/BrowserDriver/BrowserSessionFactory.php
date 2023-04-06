<?php

declare(strict_types=1);


namespace App\Domain\SharedContext\BrowserDriver;

use Psr\Log\LoggerInterface;

class BrowserSessionFactory
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    private function createBrowserSession(BrowserSessionInterface $browserSession): BrowserSessionInterface
    {
        return new LoggerBrowserSession($browserSession, $this->logger);
    }

    public function createCurlBrowserSession(): BrowserSessionInterface
    {
        return $this->createBrowserSession(
            new CurlBrowserSession()
        );
    }

}
