<?php

namespace Entrepot\SDK\Test;

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Entrepot\SDK\Client;

/**
 * @coversDefaultClass \Entrepot\SDK\Payments
 */
class PaymentsTest extends TestCase
{
    public static $client;

    public static function setUpBeforeClass(): void
    {
        $mock = new MockHandler([
            new Response(200, [], '{ "gateways": ["paypal", "stripe"] }'),
            new Response(200, [], '{ "orderId": "order-1", "intentId": "intent-1" }'),
        ]);
        $handlerStack = HandlerStack::create($mock);
        $httpClient = new \GuzzleHttp\Client(['handler' => $handlerStack]);
        self::$client = new Client(['clientId' => 'test'], $httpClient);
    }

    /**
     * @covers ::getAvailableGateways
     */
    public function testGetAvailableGateways()
    {
        $gateways = self::$client->payments->getAvailableGateways();
        $this->assertSame($gateways[0], 'paypal');
        $this->assertSame($gateways[1], 'stripe');
    }

    /**
     * @covers ::createIntent
     */
    public function testCreateIntent()
    {
        $result = self::$client->payments->createIntent('stripe');
        $this->assertSame($result['orderId'], 'order-1');
        $this->assertSame($result['intentId'], 'intent-1');
    }
}
