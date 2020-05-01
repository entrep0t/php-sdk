<?php

namespace Entrepot\SDK;

use GuzzleHttp\Psr7;
use GuzzleHttp\Exception\RequestException;

class Client
{
    private $defaultConfig;
    private $httpClient;
    public $customConfig;

    public $products;
    public $cart;

    /**
     * @param array[mixed] $customConfig - Your entrepot config
     */
    public function __construct($customConfig)
    {
        $this->defaultConfig = [
            'apiUrl' => 'https://api.entrepot.local:10000/api/v1/store',
            'clientId' => null,
            'redirectUri' => null,
            'requestsTimeout' => 30,
            'cookieNames' => [
                'accessToken' => 'entrepotAccessToken',
                'refreshToken' => 'entrepotRefreshToken',
                'sessionId' => 'entrepotSession'
            ],
            'cookieOptions' => [
                'path' => '/',
                'domain' => '',
                'expires' => 90,
                'secure' => true,
                'sameSite' => 'Strict',
            ]
        ];
        $this->customConfig = $customConfig;
        $this->httpClient = new \GuzzleHttp\Client();

        $this->products = new Products($this);
        $this->cart = new Cart($this);
    }

    /**
     * @param string $path - Config value path, ex: cookieNames.accessToken
     * @return mixed - Desired config value
     */
    public function getConfig($path)
    {
        return Utils::get($this->customConfig, $path, Utils::get($this->defaultConfig, $path));
    }

    /**
     * @param array[mixed] $options - Request options
     * @return array[mixed] - Response from api, always json
     */
    public function request($options)
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $method = $options['method'] ?? 'GET';
        $headers = array_merge($options['headers'] ?? [], [
            'Content-Type' => 'application/json; charset=utf-8',
            'Store' => $this->getConfig('clientId')
        ]);

        // Add session id to headers if found
        $sessionName = $this->getConfig('cookieNames.sessionId');
        if (isset($_SESSION[$sessionName]) && (!isset($options['session']) || $options['session'] !== false)) {
            $headers['Session'] = $_SESSION[$sessionName];
        }

        // Add authorization if found
        $accessTokenName = $this->getConfig('cookieNames.accessToken');
        if (isset($_COOKIE[$accessTokenName]) && (!isset($options['auth']) || $options['auth'] !== false)) {
            $headers['Authorization'] = 'Bearer ' . base64_encode($_COOKIE[$accessToken]);
        }

        try {
            $response = $this->httpClient->request($method, $options['url'], array_merge($options, [
                'headers' => $headers,
                'timeout' => $this->getConfig('requestsTimeout'),
            ]));
        } catch (RequestException $error) {
            if ($error->hasResponse()) {
                echo Psr7\str($error->getResponse());
            }
        }

        // Update saved session id if found in response headers
        if ($response->hasHeader('session')) {
            $_SESSION[$this->getConfig('cookieNames.sessionId')] = $response->getHeader('session');
        }

        return json_decode($response->getBody(), true);
    }
}
