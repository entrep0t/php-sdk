<?php

namespace Entrepot\SDK\Test;

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Exception\RequestException;
use Entrepot\SDK\Client;

class ClientTest extends TestCase
{
    public static $client;

    public static function setUpBeforeClass(): void
    {
        $mock = new MockHandler([
            new Response(200, [], '{ "foo": "bar" }'),
            new RequestException(
                'Unauthorized',
                new Request('GET', '/test/retry'),
                new Response(403, [], '{ "error": "unauthorized" }')
            ),
            new Response(200, [], '{ "accessToken": "test", "refreshToken": "test" }'),
            new Response(200, [], '{ "foo": "bar" }'),
        ]);
        $handlerStack = HandlerStack::create($mock);
        $httpClient = new \GuzzleHttp\Client(['handler' => $handlerStack]);
        self::$client = new Client(['clientId' => 'test'], $httpClient);
    }

    /**
     * @covers \Entrepot\SDK\Client::getConfig
     */
    public function testGetConfig()
    {
        $result = self::$client->getConfig('clientId');
        $this->assertSame($result, 'test');
    }

    /**
     * @covers \Entrepot\SDK\Client::writeTokens
     * @covers \Entrepot\SDK\Client::getAccessToken
     * @covers \Entrepot\SDK\Client::getRefreshToken
     */
    public function testWriteTokens()
    {
        self::$client->writeTokens(['accessToken' => 'access test', 'refreshToken' => 'refresh test']);
        $this->assertSame(self::$client->getAccessToken(), 'access test');
        $this->assertSame(self::$client->getRefreshToken(), 'refresh test');
    }

    /**
     * @covers \Entrepot\SDK\Client::writeSession
     * @covers \Entrepot\SDK\Client::getSessionId
     */
    public function testWriteSession()
    {
        self::$client->writeSession('test session id');
        $this->assertSame(self::$client->getSessionId(), 'test session id');
    }

    /**
     * @covers \Entrepot\SDK\Client::request
     */
    public function testRequest()
    {
        $result = self::$client->request(['url' => '/test']);
        $this->assertSame($result['foo'], 'bar');
    }

    /**
     * @covers \Entrepot\SDK\Client::requestWithRetry
     */
    public function testRequestWithRetry()
    {
        $result = self::$client->requestWithRetry(['url' => '/test']);
        $this->assertSame($result['foo'], 'bar');
    }
}
