<?php

namespace Entrepot\SDK;

class Products
{
    private $client;

    /**
     * @param Client $client - Entrepot client
     *
     * @example
     * <code>
     * $products = new Products($client);
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
     * @return array[array] Returns a list of products [
     *      'products' => array[array],
     *      'total' => int
     * ]
     *
     * @example
     * <code>
     * $products->list([
     *      'page' => 1,
     *      'count' => 10,
     *      'sort' => 'createdAt:-1',
     *      'filter' => 'categories:category-1,category-2'
     * ]);
     * </code>
     */
    public function list($params = [], $options = [])
    {
        return $this->client->request(array_merge($options, [
            'url' => $this->client->getConfig('apiUrl') . '/store/products?' . http_build_query($params)
        ]));
    }

    /**
     * @param string $id Wanted product id
     * @param array[mixed] $options (optional) Guzzle request options
     * @return array[mixed] Returns a product
     *
     * @example
     * <code>
     * $products->get('product-1');
     * </code>
     */
    public function get($id, $options = [])
    {
        $result = $this->client->request(array_merge($options, [
            'url' => $this->client->getConfig('apiUrl') . '/store/products/' . $id
        ]));

        return $result['product'];
    }
}
