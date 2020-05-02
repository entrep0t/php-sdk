<?php

namespace Entrepot\SDK\Test;

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Entrepot\SDK\Client;
use Entrepot\SDK\Cart;

class CartTest extends TestCase
{
    public static $client;
    public static $cart;

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
        self::$cart = new Cart(self::$client);
    }

    /**
     * @covers \Entrepot\SDK\Cart::get
     */
    public function testGet()
    {
        $cart = self::$cart->get();
        $this->assertSame($cart['content'][0]['id'], 'product-1');
        $this->assertSame($cart['content'][0]['quantity'], 1);
    }

    /**
     * @covers \Entrepot\SDK\Cart::addItem
     */
    public function testAddItem()
    {
        $cart = self::$cart->addItem('product-1');
        $this->assertSame($cart['content'][0]['id'], 'product-1');
        $this->assertSame($cart['content'][0]['quantity'], 2);
    }

    /**
     * @covers \Entrepot\SDK\Cart::pullItem
     */
    public function testPullItem()
    {
        $cart = self::$cart->pullItem('product-1');
        $this->assertSame($cart['content'][0]['id'], 'product-1');
        $this->assertSame($cart['content'][0]['quantity'], 1);
    }

    /**
     * @covers Cart::removeItem
     */
    public function testRemoveItem()
    {
        $cart = self::$cart->removeItem('product-1');
        $this->assertSame($cart['content'][0] ?? null, null);
    }

    /**
     * @covers \Entrepot\SDK\Cart::applyCoupon
     */
    public function testApplyCoupon()
    {
        $cart = self::$cart->applyCoupon('coupon-1');
        $this->assertSame($cart['coupons'][0]['id'], 'coupon-1');
        $this->assertSame($cart['coupons'][0]['value'], 5000);
    }

    /**
     * @covers \Entrepot\SDK\Cart::setShippingAddress
     */
    public function testSetShippingAddress()
    {
        $cart = self::$cart->setShippingAddress(
            ['firstName' => 'John', 'lastName' => 'Doe', 'address' => '1 Infinite Loop']
        );
        $this->assertSame($cart['shippingAddress']['firstName'], 'John');
        $this->assertSame($cart['shippingAddress']['lastName'], 'Doe');
        $this->assertSame($cart['shippingAddress']['address'], '1 Infinite Loop');
    }

    /**
     * @covers \Entrepot\SDK\Cart::setShippingMethod
     */
    public function testSetShippingMethod()
    {
        $cart = self::$cart->setShippingMethod('method-1', 'world');
        $this->assertSame($cart['shippingMethod']['id'], 'method-1');
    }
}
