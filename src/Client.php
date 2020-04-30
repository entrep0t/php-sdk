<?php

namespace Entrepot\SDK;

class Client
{
    private $defaultConfig;
    private $httpClient;
    public $customConfig;

    /**
     * @param array[mixed] $customConfig - Your entrepot config
     */
    public function __construct($customConfig)
    {
        $this->defaultConfig = [
            "apiUrl" => "https://api.entrepot.local:10000/api/v1/store",
            "clientId" => null,
            "redirectUri" => null,
            "fetchTimeout" => 30000,
            "cookieNames" => [
                "accessToken" => "entrepotAccessToken",
                "refreshToken" => "entrepotRefreshToken",
                "sessionId" => "entrepotSession"
            ],
            "cookieOptions" => [
                "path" => '/',
                "domain" => "",
                "expires" => 90,
                "secure" => true,
                "sameSite" => 'Strict',
            ]
        ];
        $this->customConfig = $customConfig;
        $this->httpClient = new \GuzzleHttp\Client();
        $this->products = new Products($this);
    }

    /**
     * @param string $path - Config value path, ex: cookieNames.accessToken
     * @return mixed - Desired config value
     */
    public function getConfig($path)
    {
        return Utils::get($this->customConfig, $path, Utils::get($this->defaultConfig, $path));
    }

    public function request($options)
    {
        $method = $options["method"] ?? "GET";
        $response = $this->httpClient->request($method, $options["url"], [
            "headers" => [
                "Content-Type" => "application/json; charset=utf-8",
                "Store" => $this->getConfig('clientId')
            ]
        ]);

        return json_decode($response->getBody(), true);
    }
}
