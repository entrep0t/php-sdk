<?php

namespace Entrepot\SDK\Test;

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Exception\RequestException;
use Entrepot\SDK\Client;

class CategoriesTest extends TestCase
{
    public static $client;

    public static function setUpBeforeClass(): void
    {
        $mock = new MockHandler([
            new Response(200, [], '{ "categories": [{ "id": "category-1", "name": "My category"}], "total": 1 }'),
            new Response(200, [], '{ "category": { "id": "category-1", "name": "My category"} }'),
        ]);
        $handlerStack = HandlerStack::create($mock);
        $httpClient = new \GuzzleHttp\Client(['handler' => $handlerStack]);
        self::$client = new Client(['clientId' => 'test'], $httpClient);
    }

    public function testList()
    {
        $result = self::$client->categories->list();
        $this->assertSame($result['categories'][0]['id'], 'category-1');
        $this->assertSame($result['categories'][0]['name'], 'My category');
        $this->assertSame($result['total'], 1);
    }

    public function testGet()
    {
        $category = self::$client->categories->get('category-1');
        $this->assertSame($category['id'], 'category-1');
        $this->assertSame($category['name'], 'My category');
    }
}
