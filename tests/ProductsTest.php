<?php

namespace Entrepot\SDK\Test;

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Entrepot\SDK\Client;
use Entrepot\SDK\Products;

/**
 * @coversDefaultClass \Entrepot\SDK
 */
class ProductsTest extends TestCase
{
    public static $client;
    public static $products;

    public static function setUpBeforeClass(): void
    {
        $mock = new MockHandler([
            new Response(200, [], '{ "products": [{ "id": "product-1", "name": "My product"}], "total": 1 }'),
            new Response(200, [], '{ "product": { "id": "product-1", "name": "My product"} }'),
        ]);
        $handlerStack = HandlerStack::create($mock);
        $httpClient = new \GuzzleHttp\Client(['handler' => $handlerStack]);
        self::$client = new Client(['clientId' => 'test'], $httpClient);
        self::$products = new Producst(self::$client);
    }

    /**
     * @covers Products::list
     */
    public function testList()
    {
        $result = self::$products->list();
        $this->assertSame($result['products'][0]['id'], 'product-1');
        $this->assertSame($result['products'][0]['name'], 'My product');
        $this->assertSame($result['total'], 1);
    }

    /**
     * @covers Products::get
     */
    public function testGet()
    {
        $product = self::$products->get('product-1');
        $this->assertSame($product['id'], 'product-1');
        $this->assertSame($product['name'], 'My product');
    }
}
