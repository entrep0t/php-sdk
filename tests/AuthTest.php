<?php

namespace Entrepot\SDK\Test;

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Exception\RequestException;
use Entrepot\SDK\Client;
use Entrepot\SDK\Auth;

class AuthTest extends TestCase
{
    public static $client;
    public static $auth;

    public static function setUpBeforeClass(): void
    {
        $mock = new MockHandler([
            new Response(200, [], '{ "accessToken": "access test", "refreshToken": "refresh test" }'),
            new Response(200, [], '{ "username": "user@test.com", "email": "foo@bar.com" }'),
            new RequestException(
                'Unauthorized',
                new Request('GET', '/store/auth/me'),
                new Response(403, [], '{ "error": "unauthorized" }')
            ),
            new Response(200, [], '{ "accessToken": "new access test", "refreshToken": "new refresh test" }'),
            new Response(200, [], '{ "username": "user@test.com", "email": "foo@bar.com" }'),
            new Response(
                200,
                [],
                '{ "accessToken": "registered access test", "refreshToken": "registered refresh test" }'
            ),
        ]);
        $handlerStack = HandlerStack::create($mock);
        $httpClient = new \GuzzleHttp\Client(['handler' => $handlerStack]);
        self::$client = new Client(['clientId' => 'test'], $httpClient);
        self::$auth = new Auth(self::$client);
    }

    /**
     * @covers \Entrepot\SDK\Auth::authenticate
     */
    public function testAuthenticate()
    {
        $tokens = self::$auth->authenticate('username', 'password');
        $this->assertSame($tokens['accessToken'], 'access test');
        $this->assertSame($tokens['refreshToken'], 'refresh test');
        $this->assertSame($_COOKIE[self::$client->getConfig('cookieNames.accessToken')], 'access test');
        $this->assertSame($_COOKIE[self::$client->getConfig('cookieNames.refreshToken')], 'refresh test');
    }

    /**
     * @covers \Entrepot\SDK\Auth::me
     */
    public function testMe()
    {
        $infos = self::$auth->me();
        $this->assertSame($infos['username'], 'user@test.com');
        $this->assertSame($infos['email'], 'foo@bar.com');

        $infos = self::$auth->me();
        $this->assertSame($infos['username'], 'user@test.com');
        $this->assertSame($infos['email'], 'foo@bar.com');
        $this->assertSame($_COOKIE[self::$client->getConfig('cookieNames.accessToken')], 'new access test');
        $this->assertSame($_COOKIE[self::$client->getConfig('cookieNames.refreshToken')], 'new refresh test');
    }

    /**
     * @covers \Entrepot\SDK\Auth::register
     */
    public function testRegister()
    {
        $tokens = self::$auth->register('username', 'password', 'user@email.com');
        $this->assertSame($tokens['accessToken'], 'registered access test');
        $this->assertSame($tokens['refreshToken'], 'registered refresh test');
        $this->assertSame($_COOKIE[self::$client->getConfig('cookieNames.accessToken')], 'registered access test');
        $this->assertSame($_COOKIE[self::$client->getConfig('cookieNames.refreshToken')], 'registered refresh test');
    }
}
