<?php

namespace Entrepot\SDK;

class Payments
{
    private $client;

    /**
     * @param Client $client - Entrepot client
     *
     * @example
     * <code>
     * $payments = new Payments($client);
     * </code>
     */
    public function __construct($client)
    {
        $this->client = $client;
    }

    /**
     * @param array[mixed] $options (optional) Guzzle request options
     * @return array[string] Returns all the available gateways for the current store
     *
     * @example
     * <code>
     * $payments->getAvailableGateways();
     * </code>
     */
    public function getAvailableGateways($options = [])
    {
        $result = $this->client->request(array_merge($options, [
            'url' => $this->client->getConfig('apiUrl') . '/store/payments/gateways'
        ]));

        return $result['gateways'];
    }

    /**
     * @param string $gateway The gateway you want to create a payment intent with ('paypal'|'stripe')
     * @param array[mixed] $options (optional) Guzzle request options
     * @return array[string] Returns all the available gateways for the current store
     *
     * @example
     * <code>
     * $payments->getAvailableGateways();
     * </code>
     */
    public function createIntent($gateway, $options = [])
    {
        return $this->client->request(array_merge($options, [
            'method' => 'POST',
            'url' => $this->client->getConfig('apiUrl') . '/store/payments/' . $gateway . '/intent'
        ]));
    }
}
