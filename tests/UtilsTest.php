<?php

namespace Entrepot\SDK\Test;

use PHPUnit\Framework\TestCase;
use Entrepot\SDK\Utils;

/**
 * @coversDefaultClass \Entrepot\SDK
 */
class UtilsTest extends TestCase
{
    /**
     * @covers Utils::get
     */
    public function testGet()
    {
        $arr = ['prop' => ['nestedProp' => 'foo']];
        $this->assertSame(Utils::get($arr, 'prop.nestedProp'), 'foo');
        $this->assertSame(Utils::get($arr, 'prop.foo'), null);
    }

    /**
     * @covers Utils::get
     */
    public function testGetWithDefault()
    {
        $arr = ['prop' => 'bar'];
        $this->assertSame(Utils::get($arr, 'prop'), 'bar');
        $this->assertSame(Utils::get($arr, 'prop.nestedProp.test', 'default'), 'default');
    }
}
