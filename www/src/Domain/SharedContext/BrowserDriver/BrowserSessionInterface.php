<?php

declare(strict_types=1);

namespace App\Domain\SharedContext\BrowserDriver;


interface BrowserSessionInterface
{

    public function request(Request $request): Response;

}
