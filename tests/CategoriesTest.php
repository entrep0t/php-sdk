<?php

namespace Entrepot\SDK\Test;

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Entrepot\SDK\Client;
use Entrepot\SDK\Categories;

class CategoriesTest extends TestCase
{
    public static $client;
    public static $categories;

    public static function setUpBeforeClass(): void
    {
        $mock = new MockHandler([
            new Response(200, [], '{ "categories": [{ "id": "category-1", "name": "My category"}], "total": 1 }'),
            new Response(200, [], '{ "category": { "id": "category-1", "name": "My category"} }'),
        ]);
        $handlerStack = HandlerStack::create($mock);
        $httpClient = new \GuzzleHttp\Client(['handler' => $handlerStack]);
        self::$client = new Client(['clientId' => 'test'], $httpClient);
        self::$categories = new Categories(self::$client);
    }

    /**
     * @covers \Entrepot\SDK\Categories::list
     */
    public function testList()
    {
        $result = self::$categories->list();
        $this->assertSame($result['categories'][0]['id'], 'category-1');
        $this->assertSame($result['categories'][0]['name'], 'My category');
        $this->assertSame($result['total'], 1);
    }

    /**
     * @covers \Entrepot\SDK\Categories::get
     */
    public function testGet()
    {
        $category = self::$categories->get('category-1');
        $this->assertSame($category['id'], 'category-1');
        $this->assertSame($category['name'], 'My category');
    }
}
