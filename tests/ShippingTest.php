<?php

namespace Entrepot\SDK\Test;

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Entrepot\SDK\Client;
use Entrepot\SDK\Shipping;

class ShippingTest extends TestCase
{
    public static $client;
    public static $shipping;

    public static function setUpBeforeClass(): void
    {
        $mock = new MockHandler([
            new Response(200, [], '{ "methods": [{ "id": "method-1" }], "total": 1 }'),
        ]);
        $handlerStack = HandlerStack::create($mock);
        $httpClient = new \GuzzleHttp\Client(['handler' => $handlerStack]);
        self::$client = new Client(['clientId' => 'test'], $httpClient);
        self::$shipping = new Shipping(self::$client);
    }

    /**
     * @covers \Entrepot\SDK\Shipping::list
     */
    public function testList()
    {
        $result = self::$shipping->list();
        $this->assertSame($result['methods'][0]['id'], 'method-1');
        $this->assertSame($result['total'], 1);
    }
}
