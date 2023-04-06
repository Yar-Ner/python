<?php

declare(strict_types=1);


namespace App\Domain\SharedContext\BrowserDriver;


use Psr\Log\LoggerInterface;

class LoggerBrowserSession implements BrowserSessionInterface
{
    private $nextBrowserSession;
    private $logger;

    public function __construct(
        BrowserSessionInterface $nextBrowserSession,
        LoggerInterface $logger
    ) {
        $this->logger = $logger;
        $this->nextBrowserSession = $nextBrowserSession;
    }

    public function request(Request $request): Response
    {
        $this->logger->info(sprintf('Loading page %s', $request->url));

        $response = $this->nextBrowserSession->request($request);

        $this->logger->info(sprintf('Page %s loaded with status %s', $request->url, $response->status));

        return $response;
    }
}
