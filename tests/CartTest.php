<?php

namespace Entrepot\SDK\Test;

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Exception\RequestException;
use Entrepot\SDK\Client;

class CartTest extends TestCase
{
    public static $client;

    public static function setUpBeforeClass(): void
    {
        $mock = new MockHandler([
            new Response(200, [], '{ "cart": { "content": [{ "id": "product-1", "quantity": 1}] } }'),
            new Response(200, [], '{ "cart": { "content": [{ "id": "product-1", "quantity": 2}] } }'),
            new Response(200, [], '{ "cart": { "content": [{ "id": "product-1", "quantity": 1}] } }'),
            new Response(200, [], '{ "cart": { "content": [] } }'),
            new Response(200, [], '{ "cart": { "coupons": [{ "id": "coupon-1", "value": 5000 }] } }'),
            new Response(
                200,
                [],
                '{ "cart": { "shippingAddress": { "firstName": "John", ' .
                    '"lastName": "Doe", "address": "1 Infinite Loop" } } }'
            ),
            new Response(200, [], '{ "cart": { "shippingMethod": { "id": "method-1" } } }'),
        ]);
        $handlerStack = HandlerStack::create($mock);
        $httpClient = new \GuzzleHttp\Client(['handler' => $handlerStack]);
        self::$client = new Client(['clientId' => 'test'], $httpClient);
    }

    public function testGet()
    {
        $cart = self::$client->cart->get();
        $this->assertSame($cart['content'][0]['id'], 'product-1');
        $this->assertSame($cart['content'][0]['quantity'], 1);
    }

    public function testAddItem()
    {
        $cart = self::$client->cart->addItem('product-1');
        $this->assertSame($cart['content'][0]['id'], 'product-1');
        $this->assertSame($cart['content'][0]['quantity'], 2);
    }

    public function testPullItem()
    {
        $cart = self::$client->cart->pullItem('product-1');
        $this->assertSame($cart['content'][0]['id'], 'product-1');
        $this->assertSame($cart['content'][0]['quantity'], 1);
    }

    public function testRemoveItem()
    {
        $cart = self::$client->cart->removeItem('product-1');
        $this->assertSame($cart['content'][0] ?? null, null);
    }

    public function testApplyCoupon()
    {
        $cart = self::$client->cart->applyCoupon('coupon-1');
        $this->assertSame($cart['coupons'][0]['id'], 'coupon-1');
        $this->assertSame($cart['coupons'][0]['value'], 5000);
    }

    public function testSetShippingAddress()
    {
        $cart = self::$client->cart->setShippingAddress(
            ['firstName' => 'John', 'lastName' => 'Doe', 'address' => '1 Infinite Loop']
        );
        $this->assertSame($cart['shippingAddress']['firstName'], 'John');
        $this->assertSame($cart['shippingAddress']['lastName'], 'Doe');
        $this->assertSame($cart['shippingAddress']['address'], '1 Infinite Loop');
    }

    public function testSetShippingMethod()
    {
        $cart = self::$client->cart->setShippingMethod('method-1', 'world');
        $this->assertSame($cart['shippingMethod']['id'], 'method-1');
    }
}
