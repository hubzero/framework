<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   framework
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Container\Tests;

use Hubzero\Container\Container;
use Hubzero\Container\Tests\Mock\Service;
use Hubzero\Test\Basic;

/**
 * Container test
 */
class ContainerTest extends Basic
{
	/**
	 * Test setting and getting a string
	 *
	 * @covers  \Hubzero\Container\Container::set
	 * @covers  \Hubzero\Container\Container::offsetSet
	 * @covers  \Hubzero\Container\Container::get
	 * @covers  \Hubzero\Container\Container::offsetGet
	 * @return  void
	 **/
	public function testWithString()
	{
		$container = new Container();
		$container['param'] = 'value';

		$this->assertEquals('value', $container['param']);

		$this->assertTrue($container->has('param'));

		$container->set('foo', 'bar');

		$this->assertEquals('bar', $container->get('foo'));

		$this->setExpectedException('InvalidArgumentException');

		$container->get('lorem');
	}

	/**
	 * Test setting and getting a string
	 *
	 * @covers  \Hubzero\Container\Container::set
	 * @covers  \Hubzero\Container\Container::offsetSet
	 * @covers  \Hubzero\Container\Container::get
	 * @covers  \Hubzero\Container\Container::offsetGet
	 * @return  void
	 **/
	public function testWithClosure()
	{
		$container = new Container();
		$container['service'] = function ()
		{
			return new Service();
		};

		$this->assertInstanceOf(Service::class, $container['service']);
	}

	/**
	 * Test checking for a parameter being set or not
	 *
	 * @covers  \Hubzero\Container\Container::has
	 * @covers  \Hubzero\Container\Container::offsetExists
	 * @return  void
	 **/
	public function testHas()
	{
		$container = new Container();
		$container['param'] = 'value';

		$this->assertTrue(isset($container['param']));

		$this->assertFalse(isset($container['foo']));

		$container->set('foo', 'bar');

		$this->assertTrue($container->has('foo'));

		$this->assertFalse($container->has('ipsum'));
	}

	/**
	 * Test unsetting a parameter
	 *
	 * @covers  \Hubzero\Container\Container::forget
	 * @covers  \Hubzero\Container\Container::offsetUnset
	 * @return  void
	 **/
	public function testForget()
	{
		$container = new Container();
		$container['param'] = 'value';

		$this->assertTrue(isset($container['param']));

		unset($container['param']);

		$this->assertFalse(isset($container['param']));

		$container->set('foo', 'bar');

		$this->assertTrue($container->has('foo'));

		$container->forget('foo');

		$this->assertFalse($container->has('foo'));
	}

	/**
	 * Test getting defined value names
	 *
	 * @covers  \Hubzero\Container\Container::keys
	 * @return  void
	 **/
	public function testKeys()
	{
		$container = new Container();
		$container->set('foo', 'bar');
		$container->set('bar', 'foo');

		$this->assertEquals(array('foo', 'bar'), $container->keys());
	}

	/**
	 * Test getting raw value
	 *
	 * @covers  \Hubzero\Container\Container::raw
	 * @return  void
	 **/
	public function testRaw()
	{
		$container = new Container();

		$service = function ()
		{
			return 'foo';
		};

		$container['service'] = $service;

		$this->assertSame($service, $container->raw('service'));

		$this->setExpectedException('InvalidArgumentException');

		$container->raw('lorem');
	}
}
