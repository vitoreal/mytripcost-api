<?php

namespace App\FreeCurrencyApi;

use Exception;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;

/**
 * Exposes the freecurrencyapi library to client code.
 */
class FreeCurrencyApiClient
{
    const BASE_URL = 'https://api.currencyapi.com/v3/';
    const REQUEST_TIMEOUT_DEFAULT = 15; // seconds
    const API_KEY = 'fca_live_gpoPgej0jkGkZhbWQYBmmH8eXJWGCdbwWi0jFRlV';

    protected Client $httpClient;

    public function __construct()
    {
        $guzzle_opts = [
            'http_errors' => false,
            'headers' => $this->buildHeaders(self::API_KEY),
            'timeout' => $settings['timeout'] ?? self::REQUEST_TIMEOUT_DEFAULT
        ];

        $this->httpClient = new Client($guzzle_opts);

    }

    /**
     * @throws FreeCurrencyApiException
     */
    public function call()
    {

        $endpoint = 'v3/latest?apikey=fca_live_gpoPgej0jkGkZhbWQYBmmH8eXJWGCdbwWi0jFRlV';

        $url = self::BASE_URL . $endpoint;

        try {
            $response = $this->httpClient->request('GET', $url);

        } catch (GuzzleException $e) {
            throw new FreeCurrencyApiException($e->getMessage());
        } catch (Exception $e) {
            throw new FreeCurrencyApiException($e->getMessage());
        }

        return json_decode($response->getBody(), true);
    }

    private function buildHeaders()
    {
        return [
            'user-agent' => 'Freecurrencyapi/PHP/0.1',
            'accept' => 'application/json',
            'content-type' => 'application/json',
            'apikey' => self::API_KEY,
        ];
    }
}
