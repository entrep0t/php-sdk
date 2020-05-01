<?php

namespace Entrepot\SDK;

class Shipping
{
    private $client;

    /**
     * @param Client $client - Entrepot client
     *
     * @example
     * <code>
     * $shipping = new Shipping($client);
     * </code>
     */
    public function __construct($client)
    {
        $this->client = $client;
    }

    /**
     * @param array[mixed] $params (optional) List params
     *      $params = [
     *          'page' => int,
     *          'count' => int,
     *          'sort' => string,
     *          'filter' => string
     *      ]
     * @param array[mixed] $options (optional) Guzzle request options
     * @return array[mixed] Returns a list of shipping methods [
     *      'methods' => array[array],
     *      'total' => int
     * ]
     *
     * @example
     * <code>
     * $shipping->list([
     *      'page' => 1,
     *      'count' => 10,
     *      'sort' => 'createdAt:-1',
     *      'filter' => 'slug:shipping-method-1'
     * ]);
     * </code>
     */
    public function list($params = [], $options = [])
    {
        return $this->client->request(array_merge($options, [
            'url' => $this->client->getConfig('apiUrl') . '/store/shipping/methods?' . http_build_query($params)
        ]));
    }
}
