<?php

namespace Entrepot\SDK;

class Cart
{
    private $client;

    /**
     * @param Client $client - Entrepot client
     *
     * @example
     * <code>
     * $cart = new Cart($client);
     * </code>
     */
    public function __construct($client)
    {
        $this->client = $client;
    }

    /**
     * @param array[mixed] $options (optional) Guzzle request options
     * @return array[mixed] Returns the current cart (or null if no item has been added yet)
     *
     * @example
     * <code>
     * $cart->get();
     * </code>
     */
    public function get($options = [])
    {
        $result = $this->client->request(array_merge($options, [
            'url' => $this->client->getConfig('apiUrl').'/store/cart'
        ]));

        return $result['cart'];
    }

    /**
     * @param string $productId The product you want to add to the current cart
     * @param string $variationId (optional) If it's a variation, you may also pass
     *                                         the variationId alongside productId
     * @param array[mixed] $options (optional) Guzzle request options
     * @return array[mixed] Returns the current cart (or a new one)
     *
     * @example
     * <code>
     * $cart->addItem('product-1');
     * </code>
     */
    public function addItem($productId, $variationId = null, $options = [])
    {
        $result = $this->client->request(array_merge($options, [
            'method' => 'POST',
            'url' => $this->client->getConfig('apiUrl').'/store/cart',
            'json' => [
                'command' => 'add',
                'productId' => $productId,
                'variationId' => $variationId,
                'quantity' => 1,
            ]
        ]));

        return $result['cart'];
    }

    /**
     * @param string $productId The product you want to pull from the current cart
     * @param string $variationId (optional) If it's a variation, you may also pass
     *                                       the variationId alongside productId
     * @param array[mixed] $options (optional) Guzzle request options
     * @return array[mixed] Returns the current cart (or a new one)
     *
     * @example
     * <code>
     * $cart->pullItem('product-1');
     * </code>
     */
    public function pullItem($productId, $variationId = null, $options = [])
    {
        $result = $this->client->request(array_merge($options, [
            'method' => 'POST',
            'url' => $this->client->getConfig('apiUrl').'/store/cart',
            'json' => [
                'command' => 'remove',
                'productId' => $productId,
                'variationId' => $variationId,
                'quantity' => 1,
            ]
        ]));

        return $result['cart'];
    }

    /**
     * @param string $productId The product you want to remove completely from the current cart
     * @param string $variationId (optional) If it's a variation, you may also pass
     *                                       the variationId alongside productId
     * @param array[mixed] $options (optional) Guzzle request options
     * @return array[mixed] Returns the current cart (or a new one)
     *
     * @example
     * <code>
     * $cart->removeItem('product-1');
     * </code>
     */
    public function removeItem($productId, $variationId = null, $options = [])
    {
        $result = $this->client->request(array_merge($options, [
            'method' => 'POST',
            'url' => $this->client->getConfig('apiUrl').'/store/cart',
            'json' => [
                'command' => 'set',
                'productId' => $productId,
                'variationId' => $variationId,
                'quantity' => 0,
            ]
        ]));

        return $result['cart'];
    }
}
