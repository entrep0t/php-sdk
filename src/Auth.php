<?php

namespace Entrepot\SDK;

class Auth
{
    private $client;

    /**
     * @param Client $client - Entrepot client
     *
     * @example
     * <code>
     * $auth = new Auth($client);
     * </code>
     */
    public function __construct($client)
    {
        $this->client = $client;
    }

    /**
     * @param string $username - Customer username
     * @param string $password - Customer password
     * @param array[mixed] $options (optional) Guzzle request options
     * @return array[mixed] Returns generated auth tokens
     *
     * @example
     * <code>
     * $auth->authenticate('username@email.com', 'password123');
     * </code>
     */
    public function authenticate($username, $password, $options = [])
    {
        $tokens = $this->client->request(array_merge($options, [
            'method' => 'POST',
            'url' => $this->client->getConfig('apiUrl').'/store/auth/token',
            'json' => [
                'grantType' => 'password',
                'username' => $username,
                'password' => $password,
                'clientId' => $this->client->getConfig('clientId'),
                'redirectUri' => $this->client->getConfig('redirectUri')
            ]
        ]));

        $this->client->writeTokens($tokens);

        return $tokens;
    }

    /**
     * @param array[mixed] $options (optional) Guzzle request options
     * @return array[mixed] Returns infos on the current authenticated user
     *
     * @example
     * <code>
     * $auth->me();
     * </code>
     */
    public function me($options = [])
    {
        $result = $this->client->requestWithRetry(array_merge($options, [
            'method' => 'GET',
            'url' => $this->client->getConfig('apiUrl') . '/store/auth/me',
        ]));

        return $result;
    }

    /**
     * @param string $username - Customer username
     * @param string $password - Customer password
     * @param string $email - Customer email
     * @param array[mixed] $options (optional) Guzzle request options
     * @return array[mixed] Returns generated auth tokens for the newly created user
     *
     * @example
     * <code>
     * $auth->register('username', 'password123', 'user@email.com');
     * </code>
     */
    public function register($username, $password, $email, $options = [])
    {
        $tokens = $this->client->request(array_merge($options, [
            'method' => 'POST',
            'url' => $this->client->getConfig('apiUrl').'/store/auth/register',
            'json' => [
                'username' => $username,
                'email' => $email,
                'password' => $password,
            ]
        ]));

        $this->client->writeTokens($tokens);

        return $tokens;
    }
}
