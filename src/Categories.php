<?php

namespace Entrepot\SDK;

class Categories
{
    private $client;

    /**
     * @param Client $client - Entrepot client
     *
     * @example
     * <code>
     * $categories = new Categories($client);
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
     * @return array[array] Returns a list of categories
     *
     * @example
     * <code>
     * $categories->list(['page' => 1, 'count' => 10, 'sort' => 'createdAt:-1', 'filter' => 'slug:my-category']);
     * </code>
     */
    public function list($params = [], $options = [])
    {
        return $this->client->request(array_merge($options, [
            'url' => $this->client->getConfig('apiUrl').'/store/categories?' . http_build_query($params)
        ]));
    }

    /**
     * @param string $id Wanted category id
     * @param array[mixed] $options (optional) Guzzle request options
     * @return array[mixed] Returns a category
     *
     * @example
     * <code>
     * $categories->get('category-1');
     * </code>
     */
    public function get($id, $options = [])
    {
        $result = $this->client->request(array_merge($options, [
            'url' => $this->client->getConfig('apiUrl').'/store/categories/'.$id
        ]));

        return $result['category'];
    }
}
