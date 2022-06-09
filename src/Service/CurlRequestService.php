<?php

namespace App\Service;

class CurlRequestService
{
    private const DEFAULT_HEADER = 'Content-Type: application/json';

    public function send(string $url, string $method = 'POST', ?array $headers = null, ?string $body = null): ?array
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_POSTFIELDS => $body,
            CURLOPT_HTTPHEADER => $this->prepareHeaders($headers)
        ));

        $response = curl_exec($curl);
        curl_close($curl);

        return $response ? json_decode($response, true) : null;
    }

    private function prepareHeaders(?array $headers): array
    {
        return array_merge($headers ?? [], [self::DEFAULT_HEADER]);
    }
}