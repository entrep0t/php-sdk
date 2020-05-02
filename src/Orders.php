<?php

namespace Entrepot\SDK;

class Orders
{
    private $client;

    /**
     * @param Client $client - Entrepot client
     *
     * @example
     * <code>
     * $orders = new Orders($client);
     * </code>
     */
    public function __construct($client)
    {
        $this->client = $client;
    }

    /**
     * @param string $id Wanted order id
     * @param array[mixed] $options (optional) Guzzle request options
     * @return array[mixed] Returns an order
     *
     * @example
     * <code>
     * $orders->get('order-1');
     * </code>
     */
    public function get($id, $options = [])
    {
        $result = $this->client->requestWithRetry(array_merge($options, [
            'url' => $this->client->getConfig('apiUrl') . '/store/orders/' . $id
        ]));

        return $result['order'];
    }

    /**
     * @param string $orderId Wanted order id
     * @param string $gateway Gateway used to make the payment for a this order
     * @param array[mixed] $options (optional) Guzzle request options
     * @return array[mixed] Returns an order
     *
     * @example
     * <code>
     * $orders->confirm('order-1', 'paypal');
     * </code>
     */
    public function confirm($orderId, $gateway, $options = [])
    {
        $result = $this->client->requestWithRetry(array_merge($options, [
            'method' => 'POST',
            'url' => $this->client->getConfig('apiUrl') . '/store/payments/' . $gateway . '/intent/confirm/' . $orderId
        ]));

        return $result['order'];
    }
}
