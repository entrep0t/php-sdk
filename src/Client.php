<?php

namespace Entrepot\SDK;

use GuzzleHttp\Psr7;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;

class Client
{
    private $defaultConfig;
    private $httpClient;
    private $customConfig;

    public $auth;
    public $cart;
    public $categories;
    public $orders;
    public $products;

    /**
     * @param array[mixed] $customConfig Your custom entrepot config
     *      $customConfig = [
     *          'clientId' => string (required)
     *      ]
     *
     * @example
     * <code>
     * $client = new Client(['clientId' => 'yourClientId']);
     * </code>
     */
    public function __construct($customConfig)
    {
        $this->defaultConfig = [
            'apiUrl' => 'https://api.entrepot.local:10000/api/v1',
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
                'expire' => 90 * 24 * 60 * 60, // 90 days
                'secure' => true,
                'samesite' => 'Strict',
            ]
        ];
        $this->customConfig = $customConfig;
        $this->httpClient = new \GuzzleHttp\Client();

        $this->auth = new Auth($this);
        $this->cart = new Cart($this);
        $this->categories = new Categories($this);
        $this->orders = new Orders($this);
        $this->products = new Products($this);
    }

    /**
     * @param string $path Config value path, ex: cookieNames.accessToken
     * @return mixed Desired config value
     *
     * @example
     * <code>
     * $client->getConfig('cookieOptions.secure');
     * </code>
     */
    public function getConfig($path)
    {
        return Utils::get($this->customConfig, $path, Utils::get($this->defaultConfig, $path));
    }

    /**
     * @param array[mixed] $tokens OAuth tokens to write in cookies
     *
     * @example
     * <code>
     * $client->writeTokens(['accessToken' => 'access-token', 'refreshToken' => 'refresh-token']);
     * </code>
     */
    public function writeTokens($tokens)
    {
        $cookieOptions = array_merge($this->getConfig('cookieOptions'), [
            "expire" => time() + $this->getConfig('cookieOptions.expire')
        ]);

        if (isset($tokens['accessToken'])) {
            setcookie($this->getConfig('cookieNames.accessToken'), $tokens['accessToken'], $cookieOptions);
        }

        if (isset($tokens['refreshToken'])) {
            setcookie($this->getConfig('cookieNames.refreshToken'), $tokens['refreshToken'], $cookieOptions);
        }
    }

    /**
     * @return string Refresh token from cookies
     *
     * @example
     * <code>
     * $client->getRefreshToken();
     * </code>
     */
    private function getRefreshToken()
    {
        return $_COOKIE[$this->getConfig('cookieNames.refreshToken')];
    }

    /**
     * @param array[mixed] $options Request options
     * @return array[mixed] Response from api, always json
     *
     * @example
     * <code>
     * $client->request(['method' => 'GET', 'url' => 'https://google.fr']);
     * </code>
     */
    public function request($options)
    {
        if ($this->getConfig('clientId') === null) {
            throw new \Exception(
                'Client ID was not set in configuration. Please use ' .
                '`$client = new Client([ \'clientId\': \'your_client_id\' ]);` ' .
                'in order to retrieve your entrepot objects.'
            );
        }

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

        $response = $this->httpClient->request($method, $options['url'], array_merge($options, [
            'headers' => $headers,
            'timeout' => $this->getConfig('requestsTimeout'),
        ]));

        // Automatically update saved session id if found in response headers
        if ($response->hasHeader('session')) {
            $_SESSION[$this->getConfig('cookieNames.sessionId')] = $response->getHeader('session');
        }

        return json_decode($response->getBody(), true);
    }

    /**
     * @param array[mixed] $options Request options
     * @return array[mixed] Response from api, always json
     *
     * @example
     * <code>
     * $client->requestWithRetry(['method' => 'GET', 'url' => 'https://google.fr']);
     * </code>
     */
    public function requestWithRetry($options)
    {
        try {
            $response = $this->request($options);
        } catch (ClientException $error) {
            if ($error->getStatusCode() === 403 && $this->getRefreshToken() !== null) {
                $tokens = $this->httpClient->request('POST', $this->getConfig('apiUrl') . '/store/auth/token', [
                    'json' => [
                        'grantType' => 'refresh_token',
                        'refreshToken' => $this->getRefreshToken(),
                        'clientId' => $this->getConfig('clientId'),
                        'redirectUri' => $this->getConfig('redirectUri')
                    ]
                ]);

                $this->writeTokens($tokens);
                $response = $this->request($options);
            } else {
                throw $error;
            }
        }

        return $response;
    }
}
