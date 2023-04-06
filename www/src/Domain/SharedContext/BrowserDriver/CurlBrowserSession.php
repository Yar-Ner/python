<?php

declare(strict_types=1);

namespace App\Domain\SharedContext\BrowserDriver;


class CurlBrowserSession implements BrowserSessionInterface
{
    private const CURL_HEADERS = [CURLOPT_HTTPAUTH, CURLOPT_USERPWD];

    public function request(Request $request): Response
    {
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $request->url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);

        if ($request->method === 'POST') {
          curl_setopt($curl, CURLOPT_POST, 1);
          curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($request->data));
          curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        }

        foreach ($request->headers as $key => $header) {
          if (in_array($key, self::CURL_HEADERS, true)) {
            curl_setopt($curl, $key, $header);
          }
        }

        $result = curl_exec($curl);
        $code = curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
        curl_close($curl);

        return Response::make($code, $request->url, $result);
    }
}
