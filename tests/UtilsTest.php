<?php

namespace Entrepot\SDK\Test;

use PHPUnit\Framework\TestCase;
use Entrepot\SDK\Utils;

/**
 * @coversDefaultClass \Entrepot\SDK\Utils
 */
class UtilsTest extends TestCase
{
    /**
     * @covers ::get
     */
    public function testGet()
    {
        $arr = ['prop' => ['nestedProp' => 'foo']];
        $this->assertSame(Utils::get($arr, 'prop.nestedProp'), 'foo');
        $this->assertSame(Utils::get($arr, 'prop.foo'), null);
    }

    /**
     * @covers ::get
     */
    public function testGetWithDefault()
    {
        $arr = ['prop' => 'bar'];
        $this->assertSame(Utils::get($arr, 'prop'), 'bar');
        $this->assertSame(Utils::get($arr, 'prop.nestedProp.test', 'default'), 'default');
    }
}
