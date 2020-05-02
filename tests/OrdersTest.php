<?php

namespace Entrepot\SDK\Test;

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Entrepot\SDK\Client;

/**
 * @coversDefaultClass \Entrepot\SDK\Orders
 */
class OrdersTest extends TestCase
{
    public static $client;

    public static function setUpBeforeClass(): void
    {
        $mock = new MockHandler([
            new Response(200, [], '{ "order": { "id": "order-1", "status": "pending" } }'),
            new Response(200, [], '{ "order": { "id": "order-1", "status": "paid" } }'),
        ]);
        $handlerStack = HandlerStack::create($mock);
        $httpClient = new \GuzzleHttp\Client(['handler' => $handlerStack]);
        self::$client = new Client(['clientId' => 'test'], $httpClient);
    }

    /**
     * @covers ::get
     */
    public function testGet()
    {
        $order = self::$client->orders->get('order-1');
        $this->assertSame($order['id'], 'order-1');
        $this->assertSame($order['status'], 'pending');
    }

    /**
     * @covers ::confirm
     */
    public function testConfirm()
    {
        $order = self::$client->orders->confirm('order-1', 'paypal');
        $this->assertSame($order['id'], 'order-1');
        $this->assertSame($order['status'], 'paid');
    }
}
