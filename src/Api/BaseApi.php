<?php

namespace CODEIQ\Virtualizor\Api;

use CODEIQ\Virtualizor\Exceptions\VirtualizorApiException;
use Illuminate\Support\Facades\Log;

abstract class BaseApi
{
    protected string $key;

    protected string $pass;

    protected string $ip;

    protected string $port;

    public function __construct(string $key, string $pass, string $ip, string $port)
    {
        $this->key = $key;
        $this->pass = $pass;
        $this->ip = $ip;
        $this->port = $port;
    }

    protected function makeRequest(string $endpoint, array $params = [], string $method = 'GET'): array
    {
        $url = "https://{$this->ip}:{$this->port}/{$endpoint}";
        $url .= (strstr($url, '?') ? '' : '?');

        if ($this instanceof AdminApi) {
            $url .= '&adminapikey='.rawurlencode($this->key).'&adminapipass='.rawurlencode($this->pass);
        } else {
            $url .= '&apikey='.rawurlencode($this->key).'&apipass='.rawurlencode($this->pass);
        }
        $url .= '&api=serialize';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        }

        $response = curl_exec($ch);
        $error = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        if (config('virtualizor.debug')) {
            Log::debug('Virtualizor API Response', [
                'url' => $url,
                'params' => $params,
                'response' => $response,
                'http_code' => $httpCode,
            ]);
        }

        if ($error) {
            throw new VirtualizorApiException("CURL Error: $error");
        }

        if ($httpCode >= 400) {
            throw new VirtualizorApiException("HTTP Error: $httpCode");
        }

        $data = @unserialize($response);

        if ($data === false) {
            throw new VirtualizorApiException('Invalid response format', ['response' => $response]);
        }

        if (! empty($data['error'])) {
            $errorMessage = is_array($data['error'])
                ? json_encode($data['error'])
                : (string) $data['error'];

            throw new VirtualizorApiException($errorMessage, ['response' => $data]);
        }

        return $data;
    }
}
